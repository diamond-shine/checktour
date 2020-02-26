<?php

namespace App\Http\Requests\TourOption;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $tourId = $this->route('tourOption')->id;

        return [
            'name'      => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'bookeo_id' => 'required|array',
            'bookeo_id.*' => 'string|max:64',

            // 'bookeo_id' => [
            //     'required','string','max:255',
            //     (new Unique('tours', 'bookeo_id'))
            //         ->ignore($tourId)
            // ],
        ];
    }
}
