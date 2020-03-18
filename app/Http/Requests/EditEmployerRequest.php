<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Input;

class EditEmployerRequest extends Request
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
            'id' => 'required|numeric',
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,'.Input::get('id'),
            'login' => 'required|alpha_dash|unique:users,login,'.Input::get('id').'|min:4|max:32',
            'password' => 'nullable|string|confirmed|min:4',
            'password_confirmation' => 'nullable|string',
            'role_id' => 'required|numeric',
        ];
    }
}
