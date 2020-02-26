<?php

namespace Control\Packages\FileManager\Http\Requests\Folders;

use Shelter\Kernel\Http\AbstractFormRequest;

/**
 * Class StoreRequest
 * @package Control\Packages\FileManager\Http\Requests\Folders
 *
 * @property string $name
 */
class StoreRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
        ];
    }
}
