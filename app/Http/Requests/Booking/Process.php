<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class Process extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'arrived_waiting_room' => 'required|boolean',
            'schedule_id' => 'nullable|numeric',
            'comment' => 'nullable|string|min:4|max:500'
        ];
    }
}
