<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'language'      => ['string',   'required'],
            'name'          => ['string',   'required'],
            'email'         => ['email',    'required'],
            'password'      => ['string',   'required'],
            'terms_user'    => ['boolean',  'required'],
            'company'       => ['string',   'exists:company,hash_code', 'required'],
            'country'       => ['string',   'required']
        ];
    }
}
