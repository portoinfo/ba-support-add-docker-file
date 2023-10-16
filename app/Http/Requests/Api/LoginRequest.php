<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'loginUnknown'  => ['boolean', 'required'],
            'email'         => ['email',  'required_unless:loginUnknown,1', 'nullable'],
            'password'      => ['string', 'required_unless:loginUnknown,1', 'nullable'],
            'company'       => ['string', 'exists:company,hash_code', 'required'],
            'country'       => ['string',   'required'],
            'language'      => ['string',   'required'],
        ];
    }
}
