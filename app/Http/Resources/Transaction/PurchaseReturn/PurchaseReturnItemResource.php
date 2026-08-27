<?php

namespace App\Http\Resources\Transaction\PurchaseReturn;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReturnItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $price      = $this->purchase->purchase_price_inc_tax != null ? (float)$this->purchase->purchase_price_inc_tax : 0;
        $subtotal   = $price * $this->return_qty;

        return [
            'id'                    => $this->id,
            'variation'             => array(
                'id'                    => $this->purchase->variation->id ?? '',
                'name'                  => $this->purchase->variation->name ?? ''
            ),
            'product'               => array(
                'id'                    => $this->purchase->product->id ?? '',
                'name'                  => $this->purchase->product->name ?? ''
            ),
            'price'                 => $price,
            'qty'                   => (int)$this->return_qty,
            'unit'                  => array(
                'id'                    => $this->unit->id ?? '',
                'name'                  => $this->unit->name ?? '',
                'qty'                   => (int)$this->unit_qty
            ),
            'first_unit'            => array(
                'name'                  => $this->purchase->variation->unit->name ?? '',
                'id'                    => $this->purchase->variation->unit->id ?? null,
            ),
            'subtotal'              => (float)$subtotal
        ];
    }
}
