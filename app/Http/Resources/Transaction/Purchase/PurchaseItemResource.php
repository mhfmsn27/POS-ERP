<?php

namespace App\Http\Resources\Transaction\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
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
            'item_id'               => $this->id,
            'variation_id'          => $this->variation_id,
            'product_id'            => $this->product_id,
            'name'                  => $this->variation->full_name ?? '',
            'qty'                   => (int)$this->unit_qty,
            'unit_price'            => (float)$this->purchase_price,
            'discount_amount'       => (int)$this->discount_amount,
            'total_discount'        => (int)$this->total_discount, 
            'total_tax'             => (float)$this->tax_total,
            'without_discount'      => (float)$this->without_discount,
            'purchase_price_inc_tax' => (float)$this->purchase_price_inc_tax,
            'tax'                   => (int)$this->item_tax, 
            'discount_type'         => $this->discount_type, 
            'unit'                  => $this->unit_id,
            'subtotal'              => (float)($this->quantity * $this->purchase_price_inc_tax),
            'subunits'              => $this->variation->unit != null ? $this->variation->unit->unit_turunan : array(),
            'unit_detail'           => array(
                'id'                    => $this->unit->id ?? '',
                'name'                  => $this->unit->name ?? '',
                'price'                 => $this->price_into_unit,
            ),
            'first_unit'            => array(
                'name'                  => $this->variation->unit->name ?? '',
                'id'                    => $this->variation->unit->id ?? null,
            ),  
            'qty_no_unit'           => (int)$this->quantity,
            'qty_detail'            => array(
                'qty_return'            => (int)$this->qty_return,
                'can_return'            => (int)$this->qty_can_return,
                'qty'                   => (int)$this->quantity
            )
        ];
    }
}
