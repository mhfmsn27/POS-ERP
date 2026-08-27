<?php

namespace App\Http\Resources\Transaction\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingProductItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $units      = $this->variation->unit != null ? $this->variation->unit->unit_turunan : array();
        
        return [
            "id"                => $this->id,
            'transaction_id'    => $this->transaction_id,
            'variation_id'      => $this->variation_id,
            'product_id'        => $this->product_id,
            "name"              => $this->item_name ?? ($this->variation->full_name ?? ''),
            'item_position'     => (int)$this->item_position,
            'qty'               => (int)$this->unit_qty,
            'unit_price'        => (int)$this->unit_price,
            'without_discount'  => (int)$this->unit_price_before_disc, 
            'purchase_price'    => (float)$this->variation->modal_price,
            'discount_amount'   => (int)$this->disc_amount,
            'discount'          => (int)$this->disc_amount,
            'tax'                 => 0,
            'total_discount'      => 0,
            'discount_type'       => $this->discount_type,
            'total_tax'           => 0,
            'unit'                => $this->unit->id ?? '',
            'unit_name'           => $this->unit->name ?? '',
            'subtotal'            => (int)$this->subtotal,
            'stock'               => (int)$this->variation->stock->sum('qty_available'),
            'subunits'            => $units
        ];
    }
}
