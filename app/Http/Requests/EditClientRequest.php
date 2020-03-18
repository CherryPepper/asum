<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Input;

class EditClientRequest extends Request
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
        $validation = [
            'id' => 'required|numeric',
            'first_name' => 'required|string', //|min:3
            'last_name' => 'required|string', //|min:3
            'email' => 'required|unique:users,email,'.Input::get('id'), //|email
            'passport' => 'required|string',
            'passport_mvd' => 'required|string',
            'address.region' => 'required|numeric',
            'address.street' => 'required|numeric',
            'address.home' => 'required|numeric',
            'rate_id' => 'required|numeric',
            'contract' => 'required|string',
            'login' => 'required|alpha_dash|unique:users,login,'.Input::get('id').'|min:4|max:32',
            'password' => 'nullable|string|confirmed|min:4',
            'password_confirmation' => 'nullable|string',
        ];

        for ($i = 1; $i <= 4; $i++){
            if(isset(Input::get('other_meters')[$i])){
                $validation['other_meters.'.$i.'.serial'] = 'required'; //|min:4
                $validation['other_meters.'.$i.'.value'] = 'required|numeric';
            }
        }

        return $validation;
    }
}
