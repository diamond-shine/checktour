<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'tour_id' => 'required|integer',

            'tickets_list' => 'required',
            'tickets_list.*.ticket_id' => 'required|integer',
            'tickets_list.*.quantity' => 'required|numeric',

            'options_list' => 'nullable',
            'options_list.*' => 'nullable|integer',

            'phone' => 'nullable|string',

            'comment' => 'nullable|string|min:4|max:500'
        ];
    }
}
