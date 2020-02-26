<?php

namespace App\Http\Requests\TicketOption;

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
            'price'          => 'required|string|max:255',
            'tour_option_id' => 'required|numeric'
        ];
    }
}
