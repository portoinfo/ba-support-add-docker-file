<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiGetCompanyDataRequest extends FormRequest
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
            'hash_code' => ['required', 'string', 'exists:company,hash_code'],
            'origin'    => ['required', 'string'],
        ];
    }
}
