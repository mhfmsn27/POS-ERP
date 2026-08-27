<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $subtotal = $this->qty * $this->unit_price;
        return [
            'product_name'          => $this->product->name,
            'variation_name'        => $this->variation->name,
            'qty'                   => $this->qty,
            'unit_price'            => number_format($this->unit_price),
            'subtotal'              => number_format($subtotal)
        ];
    }
}
