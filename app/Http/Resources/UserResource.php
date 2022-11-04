<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'type' => 'user',
            'id' => (string) $this->resource -> getRouteKey(),
            'attributes' => [
                'name' => $this->resource->name,
                'email' => $this->resource->email
            ],
            'links' => [
                'self' => route('api.v1.users.show', $this->resource)
            ]
        ];
    }
}
