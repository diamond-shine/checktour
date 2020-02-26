<?php

namespace Packages\FileManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Packages\FileManager\Resources\FileResource;
use Ideil\LaravelFileManager\Models\File;
use Ideil\LaravelFileManager\Models\FileFolder;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

class UploadsController extends Controller
{
    /**
     * @param Request $request
     * @param FileFolder|null $folder
     * @return array
     * @throws FileNotFoundException
     */
    public function upload(Request $request, FileFolder $folder = null): array
    {
        if ($request->headers->has('x-upload-init')) {
            return $this->initUpload($request);
        }

        if ($request->headers->has('x-upload-from') && $request->headers->get('x-upload-from') === 'EDITOR') {
            return $this->editorUpload($request);
        }

        return $this->writeChunk($request, $folder);
    }

    /**
     * @return array
     */
    protected function editorUpload(Request $request): array
    {
        $file = app('file-manager')->findOrCreateFileFromRequest(
            $request->file('upload'),
            $request->headers->has('x-private-mode') ?
                app('shelter.auth')->id() :
                null
        );

        $currentFolder = $this->currentFolder($request);

        if ($currentFolder) {
            $file->update([
                'file_folder_id' => $currentFolder->id,
            ]);
        }

        return [
            'urls' => [
                'default' => $file->thumb('1024-1024'),
                '800' => $file->thumb('800-800'),
                '1024' => $file->thumb('1024-1024'),
                '1920' => $file->thumb('1920-1920'),
            ],
        ];
    }

    /**
     * @param Request $request
     * @param FileFolder|null $folder
     * @return array
     * @throws FileNotFoundException
     */
    protected function writeChunk(Request $request, FileFolder $folder = null): array
    {
        if (! $request->headers->has('x-chunk-id')) {
            abort(400, 'Can\'t upload chunk: request has no chunk id header');
        }

        if (! $request->headers->has('x-content-id')) {
            abort(400, 'Can\'t upload chunk: request has no content id header');
        }

        $chunkId = (string)$request->headers->get('x-chunk-id');
        $fileId = (string)$request->headers->get('x-content-id');
        $path = "file-manager/{$fileId}";

        if (! \Storage::exists("{$path}/state.json")) {
            abort(400, 'Can\'t upload chunk: initial state not found');
        }

        $state = \json_decode(
            (string)\Storage::get("{$path}/state.json"),
            true
        );

        \Storage::put(
            "{$path}/chunks/{$chunkId}.part",
            $request->getContent()
        );

        $uploadedChunksCount = \count(
            \Storage::files("{$path}/chunks")
        );

        $response = [
            'data' => [
                'size' => \Storage::size("{$path}/chunks/{$chunkId}.part"),
            ],
        ];

        $isDone = $state['chunksQuantity'] === $uploadedChunksCount;

        if ($isDone) {
            $filePath = $this->concatenateChunks($fileId, $state);

            /** @var File $file */
            $file = app('file-manager')->findOrCreateFileFromLocal(
                $filePath,
                $request->headers->has('x-private-mode') ?
                    app('shelter.auth')->id() :
                    null
            );

            if ($request->headers->has('x-scope-key')) {
                $file->attachToScope(
                    $request->headers->get('x-scope-key')
                );
            }

            if ($folder) {
                $file->folder()->associate($folder)->save();
            }

            \Storage::deleteDirectory($path);

            $response['data']['item'] = FileResource::make($file);
        }

        return $response;
    }

    /**
     * @param string $fileId
     * @param array $state
     * @return mixed
     */
    protected function concatenateChunks(string $fileId, array $state)
    {
        $resultFile = "file-manager/{$fileId}/complete/{$state['name']}";

        if (\Storage::exists($resultFile)) {
            \Storage::delete($resultFile);
        }

        \Storage::put($resultFile, '');

        $out = \fopen(
            \Storage::path($resultFile),
            'wb'
        );

        for ($i = 0; $i < $state['chunksQuantity']; $i++) {
            $in = \fopen(
                \Storage::path("file-manager/{$fileId}/chunks/{$i}.part"),
                'rb'
            );

            while ($line = \fgets($in)) {
                \fwrite($out, $line);
            }

            \fclose($in);
        }

        \fclose($out);

        return \Storage::path($resultFile);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    protected function initUpload(Request $request): array
    {
        if (! $request->headers->has('x-content-name')) {
            abort(400, 'Can\'t initialize file uploading: request has no content name header');
        }

        if (! $request->headers->has('x-content-length')) {
            abort(400, 'Can\'t initialize file uploading: request has no content length header');
        }

        if (! $request->headers->has('x-chunks-quantity')) {
            abort(400, 'Can\'t initialize file uploading: request has no chunks quantity header');
        }

        $name = $request->headers->get('x-content-name');
        $size = (int)$request->headers->get('x-content-length');
        $chunksQuantity = (int)$request->headers->get('x-chunks-quantity');
        $fileId = (string)Uuid::generate(4);

        \Storage::put(
            "file-manager/{$fileId}/state.json",
            \json_encode([
                'id' => $fileId,
                'name' => \base64_decode($name),
                'size' => $size,
                'chunksQuantity' => $chunksQuantity,
            ])
        );

        return [
            'data' => [
                'fileId' => $fileId,
            ],
        ];
    }

    /**
     * @param Request $request
     * @return FileFolder|null
     */
    protected function currentFolder(Request $request): ?FileFolder
    {
        if (! $request->headers->has('x-upload-cwd')) {
            return null;
        }

        $folders = explode('/',
            trim($request->headers->get('x-upload-cwd'), '/')
        );

        $rootFolder = explode(':',
            \array_shift($folders)
        );

        $currentFolder = FileFolder::whereIsRoot()
            ->where('mark', md5($rootFolder[0]))
            ->firstOrCreate([
                'mark' => md5($rootFolder[0]),
            ], [
                'name' => $rootFolder[1] ?? $rootFolder[0],
            ]);

        $path = $rootFolder[0];

        foreach ($folders as $folder) {
            $folder = explode(':', $folder);

            $path = "/{$folder[0]}";

            $currentFolder = FileFolder::firstOrCreate([
                'mark' => md5($path),
                'parent_id' => $currentFolder->id,
            ], [
                'name' => $folder[1] ?? $folder[0],
            ]);
        }

        return $currentFolder;
    }
}
