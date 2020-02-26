<?php

namespace App\Http\Requests\Excursion;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day'  => 'required|numeric|min:1|max:7',
            'tour_id'     => 'required|numeric',
            'time' => 'required|string|max:255'
        ];
    }
}
