<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'        => 'required|string|max:255',
            'bookeo_type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tickets', 'bookeo_type')
                    ->where('tour_id', $this->get('tour_id'))
                    ->whereNull('deleted_at'),
            ],
            'tour_id'     => 'required|numeric',
            'price'       => 'required|string|max:255',
            'is_active'   => 'required|boolean'
        ];
    }
}
