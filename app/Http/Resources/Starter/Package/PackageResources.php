<?php

namespace App\Http\Resources\Starter\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResources extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'price'         => (float)$this->price,
            'limit_day'     => (int)$this->limit_day,
            'description'   => $this->description,
            'details'       => PackageDetailResources::collection($this->details)
        ];
    }
}
