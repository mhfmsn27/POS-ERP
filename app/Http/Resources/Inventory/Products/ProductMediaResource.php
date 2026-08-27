<?php

namespace App\Http\Resources\Inventory\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductMediaResource extends JsonResource
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
            'id'        => $this->id,
            'url'       => asset($this->path),
            'created'   => $this->created_at->format("Y-m-d")
        ];
    }
}
