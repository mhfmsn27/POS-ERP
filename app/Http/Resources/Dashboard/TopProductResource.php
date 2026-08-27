<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class TopProductResource extends JsonResource
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
            'image'         => asset($this->variation->product->image_data ?? ''),
            'product'       => substr($this->variation->product->name ?? '',0,30),
            'quantity'      => number_format($this->quantity),
            'total'         => number_format($this->total)
        ];
    }
}
