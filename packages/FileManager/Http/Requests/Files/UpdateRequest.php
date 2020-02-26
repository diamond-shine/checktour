<?php

namespace Control\Packages\FileManager\Http\Requests\Files;

use Shelter\Kernel\Http\AbstractFormRequest;

/**
 * Class UpdateRequest
 * @package Control\Packages\FileManager\Http\Requests
 *
 * @property string|null $alt
 */
class UpdateRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'alt' => 'nullable|string|max:255',
        ];
    }
}
