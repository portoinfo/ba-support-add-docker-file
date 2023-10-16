<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDomainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'          => (integer) $this->id,
            'company_id'  => (string)  $this->company_id,
            'domain'      => (string)  $this->domain,
            'created_at'  => (string)  $this->created_at,
            'updated_at'  => (string)  $this->updated_at,
        ];
    }
}
