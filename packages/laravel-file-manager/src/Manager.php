<?php

namespace Ideil\LaravelFileManager;

use GuzzleHttp\Client;
use Ideil\LaravelFileManager\Generator;
use Ideil\LaravelFileManager\Models\File;
use Ideil\LaravelFileManager\Resolver\BaseResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Http\UploadedFile;

class Manager
{
    /**
     * @var array
     */
    protected static $resolvers;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var static
     */
    protected $defaultDisk;

    /**
     * Manager constructor
     * @param Application $app
     * @param string|null $disk
     */
    public function __construct(Application $app, string $disk = null)
    {
        $this->app = $app;

        $this->defaultDisk = $disk ?: $this->config('default_disk');
    }

    /**
     * @param string $query
     * @param null|mixed $default
     * @return mixed
     */
    protected function config(string $query, $default = null)
    {
        return $this->app['config']->get("laravel-file-manager.{$query}", $default);
    }

    /**
     * @param string $name
     * @return Manager
     */
    public function disk(string $name): Manager
    {
        $instance = new static($this->app, $name);

        return $instance;
    }

    /**
     * @param Model $entity
     * @param UploadedFile $file
     * @param string|null $label
     * @param array $conversion
     * @param array $payload
     * @param string|null $ownerId
     * @return File
     */
    public function attachFileFromRequest(
        Model $entity,
        UploadedFile $file,
        string $label = null,
        array $conversion = [],
        array $payload = [],
        string $ownerId = null
    ): File {
        return $this
            ->findOrCreateFileFromRequest($file, $ownerId)
            ->bind($entity, $label, $conversion, $payload);
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string|null $ownerId
     * @return File
     */
    public function findOrCreateFileFromRequest(UploadedFile $uploadedFile, string $ownerId = null): File
    {
        $hash = static::makeFileHash($uploadedFile);

        $query = File::hash($hash)->disk($this->defaultDisk);

        if ($ownerId) {
            $query = $query->owner($ownerId);
        }

        $file = $query->first();

        if (! $file) {
            $file = $this->newFileModel($uploadedFile, $ownerId);

            $file->hash = $hash;

            if (! $this->resolver()->putFile($file, $uploadedFile)) {
                throw new \RuntimeException('File not saved');
            }

            $this->saveFileModel($file);
        }

        return $file;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return string
     */
    public static function makeFileHash(UploadedFile $uploadedFile): string
    {
        return \md5_file(
            $uploadedFile->getRealPath()
        );
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string|null $ownerId
     * @return File
     */
    protected function newFileModel(UploadedFile $uploadedFile, string $ownerId = null): File
    {
        return new File([
            'name' => basename(
                $uploadedFile->getClientOriginalName()
            ),
            'size' => $uploadedFile->getSize(),
            'mime' => $uploadedFile->getMimeType(),
            'ext' => $uploadedFile->getExtension(),
            'disk' => $this->resolver()->getDiskInfo()['name'],
            'owner_id' => $ownerId,
        ]);
    }

    /**
     * @param string $disk
     * @return BaseResolver
     */
    protected function resolver(string $disk = null): BaseResolver
    {
        $disk = $disk ?: $this->defaultDisk;

        if (! isset(self::$resolvers[$disk])) {
            $diskInfo = [
                'name' => $disk,
                'root' => $this->app['config']->get("filesystems.disks.{$disk}.root"),
                'config' => $this->app['config']->get("filesystems.disks.{$disk}.file_manager", []),
            ];

            $resolverClass = $this->diskInfo['config']['resolver'] ?? BaseResolver::class;

            self::$resolvers[$disk] = new $resolverClass(
                $this->app['filesystem']->disk($diskInfo['name']),
                $diskInfo
            );
        }

        return self::$resolvers[$disk];
    }

    /**
     * @param File $file
     * @return File
     */
    protected function saveFileModel(File $file): File
    {
        if (\strpos($file->mime, 'image') === 0) {
            [$width, $height] = \getimagesize(
                $this->resolver()->makePath($file, true)
            );
        } else {
            $width = null;
            $height = null;
        }

        $file->fill([
            'width' => $width,
            'height' => $height,
        ]);

        $file->save();

        return $file;
    }

    /**
     * @param Model $entity
     * @param string $filePath
     * @param string|null $label
     * @param array $conversion
     * @param array $payload
     * @param string|null $ownerId
     * @return File
     */
    public function attachFileFromLocal(
        Model $entity,
        string $filePath,
        string $label = null,
        array $conversion = [],
        array $payload = [],
        string $ownerId = null
    ): File {
        return $this
            ->findOrCreateFileFromLocal($filePath, $ownerId)
            ->bind($entity, $label, $conversion, $payload);
    }

    /**
     * @param string $filePath
     * @param string|null $ownerId
     * @return File
     */
    public function findOrCreateFileFromLocal(string $filePath, string $ownerId = null): File
    {
        return $this->findOrCreateFileFromRequest(
            $this->prepareLocalFile($filePath),
            $ownerId
        );
    }

    /**
     * @param string $filePath
     * @param string|null $fileName
     * @return UploadedFile
     * @throws \RuntimeException
     */
    private function prepareLocalFile(string $filePath, string $fileName = null): UploadedFile
    {
        if (! file_exists($filePath)) {
            throw new \RuntimeException("File {$filePath} not found");
        }

        return new UploadedFile(
            $filePath,
            $fileName ?: basename($filePath),
            finfo_file(
                finfo_open(FILEINFO_MIME_TYPE),
                $filePath
            ),
            filesize($filePath)
        );
    }

    /**
     * @param Model $entity
     * @param string $fileUrl
     * @param string|null $label
     * @param array $conversion
     * @param array $payload
     * @param string|null $ownerId
     * @return File
     */
    public function attachFileFromUrl(
        Model $entity,
        string $fileUrl,
        string $label = null,
        array $conversion = [],
        array $payload = [],
        string $ownerId = null
    ): File {
        return $this
            ->findOrCreateFileFromUrl($fileUrl, $ownerId)
            ->bind($entity, $label, $conversion, $payload);
    }

    /**
     * @param string $fileUrl
     * @param string|null $ownerId
     * @return File
     */
    public function findOrCreateFileFromUrl(string $fileUrl, string $ownerId = null): File
    {
        $fileContent = $this->fetchFile($fileUrl);

        $fileInfo = \pathinfo($fileUrl);

        $tmpPath = storage_path('tmp/' . \md5($fileContent));

        $filename = mb_strpos($fileUrl, '?') === false ?
            $fileInfo['basename'] :
            'Dynamic';


        if (! @\mkdir(storage_path('tmp')) && ! \is_dir(storage_path('tmp'))) {
            throw new \RuntimeException('It is not possible to create a temporary folder');
        }

        if (\file_put_contents($tmpPath, $fileContent) === false) {
            throw new \RuntimeException("Error saving temp file [{$fileUrl}]");
        }

        $file = $this->findOrCreateFileFromRequest(
            $this->prepareLocalFile($tmpPath, $filename),
            $ownerId
        );

        @\unlink($tmpPath);

        return $file;
    }

    /**
     * @param string $fileUrl
     * @return string
     */
    public function fetchFile(string $fileUrl): string
    {
        $request = (new Client)->get($fileUrl);

        return $request->getBody()->getContents();
    }

    /**
     * @param File $file
     * @return string
     */
    public function makeUrl(File $file): string
    {
        return $this->resolver($file->disk)->makeUrl($file);
    }

    /**
     * @param File $file
     * @param string|null $resize
     * @param array $conversion
     * @return string
     */
    public function makeThumbUrl(File $file, string $resize = null, array $conversion = []): string
    {
        return $this->resolver($file->disk)->makeThumbUrl($file, $resize, $conversion);
    }

    /**
     * @param File $file
     * @param bool $full
     * @return string
     */
    public function makePath(File $file, bool $full = false): string
    {
        return $this->resolver($file->disk)->makePath($file, $full);
    }
}
