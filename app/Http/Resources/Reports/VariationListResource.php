<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationListResource extends JsonResource
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
            'name'      => $this->full_name,
            'category'  => $this->product->category->name ?? '',
            'stocks'    => (float)$this->all_stock()->sum('qty_available'),
            'price'     => (float)$this->modal_price
        ];
    }
}
