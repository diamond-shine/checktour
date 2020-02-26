<?php

namespace App\Http\Requests\TicketOption;

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
            'price'          => 'required|string|max:255',
            'ticket_id'      => 'required|numeric',
            'tour_option_id' => 'required|numeric',
        ];
    }
}
