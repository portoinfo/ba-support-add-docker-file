<?php

namespace App\Http\Resources;

use App\Tools\Client;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name'        => (string)  Client::forceCleanEmail($this->name),
            'email'       => (string)  Client::forceCleanEmail($this->email),
            'uuid'        => (string)  $this->user_uuid,
            'created_at'  => (string)  $this->created_at,
            'language'    => (string)  $this->language,
        ];
    }
}
