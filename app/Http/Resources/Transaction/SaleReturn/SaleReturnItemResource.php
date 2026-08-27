<?php

namespace App\Http\Resources\Transaction\SaleReturn;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReturnItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $price      = $this->sell->unit_price != null ? (float)$this->sell->unit_price : 0;
        $subtotal   = $price * $this->return_qty;

        return [
            'id'                    => $this->id,
            'variation'             => array(
                'id'                    => $this->sell->variation->id ?? '',
                'name'                  => $this->sell->variation->name ?? ''
            ),
            'product'               => array(
                'id'                    => $this->sell->product->id ?? '',
                'name'                  => $this->sell->product->name ?? ''
            ),
            'price'                 => $price,
            'qty'                   => (int)$this->return_qty,
            'unit'                  => array(
                'id'                    => $this->unit->id ?? '',
                'name'                  => $this->unit->name ?? '',
                'qty'                   => (int)$this->unit_qty
            ),
            'first_unit'            => array(
                'name'                  => $this->sell->variation->unit->name ?? '',
                'id'                    => $this->sell->variation->unit->id ?? null,
            ),
            'subtotal'              => (float)$subtotal
        ];
    }
}
