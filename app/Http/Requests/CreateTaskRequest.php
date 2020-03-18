<?php

namespace App\Http\Requests;

class CreateTaskRequest extends Request
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
            'address.region' => 'required|numeric',
            'address.street' => 'required|numeric',
            'address.home' => 'required|numeric',
            'role_id' => 'required|numeric',
            'employer_id' => 'required|numeric',
            'date_start' => 'required|date',
            'date_end' => 'required|date|after:date_start',
            'message' => 'required|string'
        ];
    }
}
