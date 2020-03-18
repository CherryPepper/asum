<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TozeleshMeterRequest extends FormRequest
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
            'address.region' => 'required|numeric',
            'address.street' => 'required|numeric',
            'address.home' => 'required|numeric',
            'rate_id' => 'required|numeric',
            'operator_id' => 'required|numeric',
            'latlng' => 'required'
        ];
    }
}
