<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MuriloPerosa\DomainTools\Name;

class StoreCompanyDomainRequest extends FormRequest
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
        if ($this->domain)
        {
            $name   = (new Name($this->domain))->sanitize(true);
            $this->domain = $name->domain;
        }

        return [
            'company'  => ['required', 'string', 'exists:company,hash_code'],
            'domain'   => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Customized error messages
     */
    public function messages()
    {
        if ($this->domain)
        {
            $name   = (new Name($this->domain))->sanitize(true);
            $this->domain = $name->domain;
        }

        return [
            'company.required' => __('bs-company-required'),   
            'company.string'   => __('bs-company-string'),     
            'company.exists'   => __('bs-company-exists'),     
            'domain.required'  => __('bs-domain-required'),    
            'domain.string'    => __('bs-domain-string'),      
            'domain.max'       => __('bs-domain-max'),         
            'domain.unique'    => __('bs-domain-unique') ." - ". $this->domain,
        ];
    }
}
