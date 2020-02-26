<?php

namespace Control\Packages\FileManager\Resources;

use Ideil\LaravelFileManager\Models\File;
use Control\Packages\FileManager\Resources\FileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/**
 * Class FileCollectionResource
 * @package Control\Packages\FileManager\Resources
 */
class FileCollectionResource extends ResourceCollection
{
    /**
     * @param Request $request
     * @return Collection
     */
    public function toArray($request): Collection
    {
        return $this->collection->map(function (File $file) {
            return FileResource::make($file);
        });
    }
}
