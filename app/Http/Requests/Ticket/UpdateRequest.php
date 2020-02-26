<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $ticket = $this->route('ticket')->id;
        return [
            'name'        => 'required|string|max:255',
            'bookeo_type' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tickets', 'bookeo_type')
                    ->where('tour_id', $this->get('tour_id'))
                    ->whereNull('deleted_at')
                    ->ignore($ticket),
            ],
            'tour_id'     => 'required|numeric',
            'price'       => 'required|string|max:255',
            'is_active'   => 'required|boolean'
        ];
    }
}
