<?php

namespace App\Http\Requests\Roster;

use Illuminate\Validation\Rules\Exists;
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
        $rules = [
            'comment' => 'nullable|string|min:4|max:500',
            'images' => 'nullable|array',
            'images.*.id' => [
                'nullable',
                'string',
                (new Exists('files', 'id')),
            ],
            'disabled_options' => 'nullable',
            'disabled_options*.' => 'nullable|integer'
        ];

        if (app('shelter.auth')->user()->can('rosters.permit')) {
            $rules['is_recruited'] = 'required|boolean';
        }

        if (app('shelter.auth')->user()->can('rosters.process')) {
            $rules['is_enquired'] = 'required|boolean';
            $rules['is_finished'] = 'required|boolean';
        }

        return $rules;
    }
}
