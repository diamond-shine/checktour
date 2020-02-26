<?php

namespace Packages\FileManager\Resources;

use Ideil\LaravelFileManager\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * Class FileResource
 * @package Control\Packages\FileManager\Resources
 *
 * @property File $resource
 */
class FileResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'size' => $this->resource->size,
            'alt' => $this->resource->alt,
            'mime' => $this->resource->mime,
            'name' => $this->resource->name,
            'width' => $this->resource->width,
            'height' => $this->resource->height,
            'is_image' => $this->resource->isImage(),
            'thumbs' => [
                'resize' => [
                    'small' => $this->resource->thumb('100x100'),
                    'medium' => $this->resource->thumb('500x500'),
                ],
                'canvas' => [
                    'small' => $this->resource->thumb('100-100'),
                    'medium' => $this->resource->thumb('500-500'),
                ],
                'scale_x' => [
                    'small' => $this->resource->thumb('100x'),
                    'medium' => $this->resource->thumb('500x'),
                ],
                'scale_y' => [
                    'small' => $this->resource->thumb('x100'),
                    'medium' => $this->resource->thumb('x500'),
                ],
            ],
            'url' => $this->resource->url(),
            'content' => $this->resource->mime === 'image/svg' ?
                \file_get_contents(
                    $this->resource->path(true)
                ) :
                null,
            'updated_at' => $this->updated_at->toAtomString(),
            'created_at' => $this->created_at->toAtomString(),

            'weight' => $this->whenPivotLoaded('file_shortcuts', function () {
                return $this->resource->pivot->weight;
            }),

            'used_count' => $this->resource->shortcuts_count ?: 0,

            'type' => $this->resolveType($this->resource->mime),
        ];
    }

    /**
     * @param string $mime
     * @return string
     */
    protected function resolveType(string $mime): string
    {
        if (Str::startsWith($mime, 'image/')) {
            return 'image';
        }

        switch ($mime) {
            case 'application/zip':
            case 'application/x-gzip':
                return 'archive';

            case 'text/x-php':
                return 'code';
        }

        return 'file';
    }
}
