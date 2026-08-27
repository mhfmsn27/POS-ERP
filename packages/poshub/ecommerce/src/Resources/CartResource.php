<?php

namespace Poshub\Ecommerce\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'product_name'      => $this->variation->product->name ?? '',
            'image'             => asset($this->variation->product->default_image ?? ''),
            'variation_name'    => $this->variation->name == 'no-name' ? '' : $this->variation->name,
            'price'             => (int)$this->variation->selling_price,
            'url_product'       => route('ecommerce.shop_detail', $this->variation->product_id),
            'quantity'          => (int)$this->quantity,
            'stocks'            => (int)$this->variation->stock_in_website->sum('qty_available'),
        ];
    }
}
