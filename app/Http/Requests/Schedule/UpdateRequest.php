<?php

namespace App\Http\Requests\Schedule;

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
        // $tourId = $this->route('tour')->id;

        return [
            'user_id'     => 'required|string|max:255',
            'tour_id'     => 'required|numeric',
            'assigned_at' => 'required|string|max:255'
        ];
    }
}
