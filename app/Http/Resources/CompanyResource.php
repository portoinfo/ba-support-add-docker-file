<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Tools\Crypt;

class CompanyResource extends JsonResource
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
            "id"          => Crypt::encrypt($this->id),
            "name"        => $this->name,
            "description" => $this->description,
            "logo"        => $this->logo,
            "hash_code"   => $this->hash_code,
            "status"      => $this->status
        ];
    }
}
