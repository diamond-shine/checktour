<?php

namespace App\Http\Requests\Excursion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day' => 'required|numeric|min:1|max:7',
            'time' => 'required|string|max:255'
        ];
    }
}
