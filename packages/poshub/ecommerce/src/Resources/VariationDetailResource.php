<?php

namespace Poshub\Ecommerce\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VariationDetailResource extends JsonResource
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
            'product_name'  => $this->product->name ?? '',
            'name'          => $this->name, 
            'stock'         => (int)$this->stock_in_website->sum("qty_available"),
            'id'            => $this->id,
            'price'         => (int)$this->selling_price,
        ];
    }
}
