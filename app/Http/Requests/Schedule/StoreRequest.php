<?php

namespace App\Http\Requests\Schedule;

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
            'user_id'      => 'required|string|max:255',
            'tour_id'      => 'required|numeric',
            'excursion_id' => 'required|numeric',
            'assigned_at'  => 'required|string|max:255'
        ];
    }

    public function messages()
    {
        return [
            'excursion_id.required' => _('The Day field is required'),
            'assigned_at.required' => _('The Time field is required')
        ];
    }
}
