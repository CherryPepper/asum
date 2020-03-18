<?php

namespace App\Http\Requests;

class CreateEmployerRequest extends Request
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
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => 'required|email|unique:users',
            'login' => 'required|alpha_dash|unique:users|min:4|max:32',
            'password' => 'required|string|confirmed|min:4',
            'password_confirmation' => 'required|string',
            'role_id' => 'required|numeric',
        ];
    }
}
