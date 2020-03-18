<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditObjectRequest extends FormRequest
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
            'object_id' => 'required|numeric',
            'lamps' => 'required',
            'time_on' => 'nullable|date_format:H:i',
            'time_off' => 'nullable|date_format:H:i'
        ];
    }
}
