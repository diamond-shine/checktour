<?php

namespace Packages\FileManager\Http\Requests\Explorer;

use Shelter\Kernel\Http\AbstractFormRequest;

/**
 * Class DestroySelectedRequest
 * @package Packages\FileManager\Http\Requests\Explorer
 *
 * @property array $files
 * @property array $folders
 */
class DestroySelectedRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'files' => 'array',
            'folders' => 'array'
        ];
    }
}
