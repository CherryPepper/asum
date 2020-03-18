<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Input;

class MeterEditRequest extends Request
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
            'login' => 'required|string|min:3',
            'password' => 'required|string|min:3',
            'serial' => 'required|unique:meters,serial,'.Input::get('id'),//.'|min:8',
            'ip_address' => 'required|unique:meters,ip_address,'.Input::get('id').'|min:8',
            'operator_id' => 'required|numeric',
        ];
    }
}
