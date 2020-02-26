<?php

namespace Packages\FileManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\Meta\Message;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Packages\FileManager\Http\Requests\{
    Explorer\DestroySelectedRequest,
    Explorer\MoveSelectedRequest,
    Folders\StoreRequest as FoldersStoreRequest
};

use Packages\FileManager\Resources\{
    FileResource,
    FolderResource
};

use Ideil\LaravelFileManager\Models\{
    File,
    FileFolder
};

class ExplorerController extends Controller
{
    /**
     * @param Request $request
     * @param FileFolder|null $folder
     * @return array
     */
    public function index(Request $request, FileFolder $folder = null): array
    {
        $breadcrumbs = null;

        if ($folder) {
            $breadcrumbs = FolderResource::collection(
                $folder->trace()
            );
        }

        if ($request->header('X-PRIVATE-MODE')) {
            return [
                'data' => [
                    'files' => [],
                    'folders' => [],
                    'pagination' => null,
                    'breadcrumbs' => $breadcrumbs,
                ],
            ];
        }

        $filesQuery = File::orderBy('updated_at', 'DESC')->whereNull('owner_id');
        $foldersQuery = FileFolder::defaultOrder();

        if ($folder) {
            $filesQuery->belongsToFolder($folder);
            $foldersQuery->belongsToFolder($folder);
        } else {
            $filesQuery->whereNull('file_folder_id');
            $foldersQuery->whereNull('parent_id');
        }

        if (is_valid_string($request->term)) {
            $foldersQuery->where('name', 'like', "%{$request->term}%");
        }

        if (is_valid_string($request->term)) {
            $filesQuery->where('name', 'like', "%{$request->term}%");
        }

        $itemsWithPagination = $this->makePaginationWithItems($request, $foldersQuery, $filesQuery);

        $folders = $itemsWithPagination['folders'];
        $files = $itemsWithPagination['files'];

        return [
            'data' => [
                'files' => $files ? FileResource::collection($files) : [],
                'folders' => $folders ? FolderResource::collection($folders) : [],
                'pagination' => [
                    'pagination_by' => $itemsWithPagination['pagination_by'],
                    'last_item' => $itemsWithPagination['last_item'],
                    'has_more_items' => $itemsWithPagination['has_more_items'],
                ],
                'breadcrumbs' => $breadcrumbs,
            ],
        ];
    }

    /**
     * @param Request $request
     * @param FileFolder|null $folder
     * @return array
     */
    public function loadMore(Request $request, FileFolder $folder = null): array
    {
        $filesQuery = File::orderBy('updated_at', 'DESC')->whereNull('owner_id');
        $foldersQuery = FileFolder::defaultOrder();

        if ($folder) {
            $filesQuery->belongsToFolder($folder);
            $foldersQuery->belongsToFolder($folder);
        } else {
            $filesQuery->whereNull('file_folder_id');
            $foldersQuery->whereNull('parent_id');
        }

        if (is_valid_string($request->term)) {
            $foldersQuery->where('name', 'like', "%{$request->term}%");
        }

        if (is_valid_string($request->term)) {
            $filesQuery->where('name', 'like', "%{$request->term}%");
        }

        $itemsWithPagination = $this->makePaginationWithItems($request, $foldersQuery, $filesQuery);

        $folders = $itemsWithPagination['folders'];
        $files = $itemsWithPagination['files'];

        return [
            'data' => [
                'files' => $files ? FileResource::collection($files) : [],
                'folders' => $folders ? FolderResource::collection($folders) : [],
                'pagination' => [
                    'pagination_by' => $itemsWithPagination['pagination_by'],
                    'last_item' => $itemsWithPagination['last_item'],
                    'has_more_items' => $itemsWithPagination['has_more_items'],
                ],
            ],
        ];
    }

    /**
     * @param FoldersStoreRequest $request
     * @param FileFolder|null $parent
     * @return array|JsonResponse
     */
    public function storeFolder(FoldersStoreRequest $request, FileFolder $parent = null)
    {
        if ($parent) {
            $folder = $parent->children()->create(['name' => $request->name]);
        } else {
            $folder = FileFolder::create(['name' => $request->name]);
        }

        return [
            'data' => [
                'item' => FolderResource::make($folder),
            ],
            'meta' => [
                Message::make(_('Теку успішно створено'))->success(),
            ],
        ];
    }

    /**
     * @param DestroySelectedRequest $request
     * @param FileFolder|null $folder
     * @return array|JsonResponse
     * @throws \Throwable
     */
    public function destroySelected(DestroySelectedRequest $request, FileFolder $folder = null)
    {
        try {
            \DB::transaction(function () use ($request, $folder) {
                if ($request->has('files')) {
                    $this->destroyFiles(
                        $request->get('files'),
                        $folder
                    );
                }

                if ($request->has('folders')) {
                    $this->destroyFolders(
                        $request->get('folders'),
                        $folder
                    );
                }
            });
        } catch (\LogicException $e) {
            return $this->failedResponseWithMessage(
                _('Не можливо видалити елементи які використовуються')
            );
        }

        return \array_merge(
            $this->index($request, $folder),
            [
                'meta' => [
                    Message::make(_('Вибрані елементи було видалено'))->success(),
                ],
            ]
        );
    }

    /**
     * @param MoveSelectedRequest $request
     * @param FileFolder|null $parent
     * @return array
     */
    public function moveSelected(MoveSelectedRequest $request, FileFolder $parent = null): array
    {
        if ($request->has('files')) {
            File::whereIn('id', $request->get('files'))->update([
                'file_folder_id' => $parent ? $parent->id : null,
            ]);
        }

        if ($request->has('folders')) {
            $folders = FileFolder::findMany(
                $request->get('folders')
            );

            foreach ($folders as $folder) {
                if ($parent) {
                    $parent->appendNode($folder);
                } else {
                    $folder->saveAsRoot();
                }
            }
        }

        return \array_merge(
            $this->index($request, $parent),
            [
                'meta' => [
                    Message::make(_('Вибрані елементи було переміщено'))->success(),
                ],
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     *
     * @throws HttpException
     */
    public function firstOrCreateFolder(Request $request): array
    {
        if (! $request->filled('path')) {
            abort(400);
        }

        $folders = explode('/',
            trim($request->get('path'), '/')
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

        return $this->folderInfo($currentFolder);
    }

    /**
     * @param FileFolder $folder
     * @return array
     */
    public function folderInfo(FileFolder $folder): array
    {
        return [
            'data' => [
                'item' => FolderResource::make($folder),
            ],
        ];
    }

    /**
     * @param Request $request
     * @param Builder $foldersQuery
     * @param Builder $filesQuery
     * @return array
     */
    protected function makePaginationWithItems(Request $request, Builder $foldersQuery, Builder $filesQuery): array
    {
        $hasMoreFolders = false;
        $hasMoreFiles = false;
        $folders = null;
        $files = null;

        if ($request->get('last_item', null)) {
            $foldersQuery->where('_lft', '>', $request->get('last_item', null));
        }

        if ($request->get('pagination_by', 'folders') === 'folders') {
            $folders = $foldersQuery->limit(25)->get();
        } else {
            $folders = collect();
        }

        if ($folders->count() > 24) {
            $hasMoreFolders = true;
        }

        if ($folders->count() <= 24 && $filesQuery->exists()) {
            $hasMoreFiles = true;
        }

        if ($hasMoreFolders) {
            $folders = $folders->take(24);

            return [
                'folders' => $folders,
                'files' => null,
                'pagination_by' => 'folders',
                'last_item' => $folders->last()->_lft,
                'has_more_items' => true,
            ];
        }

        if ($hasMoreFiles) {
            $files = $filesQuery->simplePaginate(24, ['*'], $folders->isEmpty() ? 'last_item' : null);

            return [
                'folders' => $folders,
                'files' => $files,
                'pagination_by' => 'files',
                'last_item' => $files->currentPage() +1,
                'has_more_items' => $files->hasMorePages(),
            ];
        }

        return [
            'folders' => $folders,
            'files' => $files,
            'pagination_by' => null,
            'last_item' => null,
            'has_more_items' => null,
        ];
    }

    /**
     * @param array $fileIds
     * @param FileFolder|null $folder
     * @return void
     */
    protected function destroyFiles(array $fileIds, FileFolder $folder = null): void
    {
        $query = File::whereIn('id', $fileIds)->withCount(['shortcuts']);

        if ($folder) {
            $query->belongsToFolder($folder);
        }

        /** @var Collection $files */
        $files = $query->get();

        $usedFiles = $files->where('shortcuts_count', '!=', 0);

        if ($usedFiles->isNotEmpty()) {
            throw new \LogicException;
        }

        File::whereIn(
            'id',
            $files->pluck('id')
        )->delete();
    }

    /**
     * @param array $folderIds
     * @param FileFolder|null $folder
     * @return void
     */
    protected function destroyFolders(array $folderIds, FileFolder $folder = null): void
    {
        $query = FileFolder::whereIn('id', $folderIds)->with(['descendants']);

        if ($folder) {
            $query->belongsToFolder($folder);
        }

        /** @var Collection $files */
        $folders = $query->get();

        $allFolderIds = $folders->pluck('id')->merge(
            $folders->pluck('descendants.*.id')->collapse()
        );

        $failedCount = File::whereIn('file_folder_id', $allFolderIds)
            ->has('shortcuts')
            ->count();

        if ($failedCount) {
            throw new \LogicException;
        }

        FileFolder::whereIn('id', $allFolderIds)->delete();
    }

    /**
     * @param Builder $query
     * @param string $acceptQuery
     * @return Builder
     */
    protected function applyAcceptFilter(Builder $query, string $acceptQuery): Builder
    {
        $accepts = \explode(',', $acceptQuery);

        $query->where(function (Builder $q) use ($accepts) {
            foreach ($accepts as $accept) {
                $acceptData = \array_map(
                    'trim',
                    \explode('/', $accept)
                );

                if (! $acceptData) {
                    continue;
                }

                if (\count($acceptData) === 1 || $acceptData[1] === '*') {
                    $q->orWhere('mime', 'like', "{$acceptData[0]}/%");
                } else {
                    $q->orWhere('mime', "{$acceptData[0]}/$acceptData[1]");
                }
            }
        });

        return $query;
    }
}
