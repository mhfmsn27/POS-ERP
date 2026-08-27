<?php

namespace App\Http\Resources\Inventory\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationListResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name == 'no-name' ? '' : $this->name,
            'purchase_price'    => floor((float)($this->modal_price + $this->tax_variation)),
            'sell_price'        => floor((int)$this->selling_price),
            'grocery'           => floor((float)$this->grocery),
            'stock'             => (float)$this->stock->sum("qty_available"),
        ];
    }
}
