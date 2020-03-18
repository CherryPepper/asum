<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeterRegistrationRequest extends FormRequest
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
            'ip_address' => [
                'required',
                Rule::unique('meters')->where(function ($query) {
                    return $query->where('status_id', '!=', 3);
                })
            ],
            'serial' => [
                'required',
                Rule::unique('meters')->where(function ($query) {
                    return $query->where('status_id', '!=', 3);
                })
            ],
            'operator_id' => 'required|numeric',
            'nst_lvl' => 'required|numeric'
        ];
    }
}
