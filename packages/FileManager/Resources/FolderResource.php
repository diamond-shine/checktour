<?php

namespace Packages\FileManager\Resources;

use Ideil\LaravelFileManager\Models\FileFolder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class FolderResource
 * @package Control\Packages\FileManager\Resources
 *
 * @property FileFolder $resource
 */
class FolderResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'mark' => $this->resource->mark,
            '_lft' => $this->resource->_lft,
            '_rgt' => $this->resource->_rgt,
        ];
    }
}
