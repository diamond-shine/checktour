<?php

namespace App\Http\Requests\Tour;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|string|max:255',
            'currency'  => 'required|string|max:255',
            'bookeo_id' => 'required|array',
            'bookeo_id.*' => 'string|max:64',
            'is_active' => 'required|boolean',
            'no_options_title' => 'nullable|string|max:255'
        ];
    }
}
