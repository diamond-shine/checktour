<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'tour_id' => 'required|integer',
            'booking_number' => 'required|string|max:255|unique:bookings',
            'start_at' => 'required|string:max:5',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'tickets_list' => 'required',
            'tickets_list.*.ticket_id' => 'required|integer',
            'tickets_list.*.quantity' => 'required|numeric',
            'options_list' => 'nullable',
            'options_list.*' => 'nullable|integer',
            'comment' => 'nullable|string|min:4|max:500'
        ];
    }
}
