<?php

namespace App\Http\Resources\Starter\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageDetailResources extends JsonResource
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
            'name'      => $this->name
        ];
    }
}
