<?php

namespace Control\Packages\FileManager\Resources\Files;

use Control\Packages\FileManager\Resources\FileResource;
use Illuminate\Http\Request;

/**
 * Class FormResource
 * @package Control\Packages\FileManager\Resources
 */
class FormResource extends FileResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return \array_merge(
            parent::toArray($request),
            [
                'used_count' => $this->shortcuts->count(),
            ]
        );
    }
}
