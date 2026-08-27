<?php

namespace App\Http\Resources\Transaction\Purchase\Po;

use Illuminate\Http\Resources\Json\JsonResource;

class PoItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $units          = $this->variation->unit != null ? $this->variation->unit->unit_turunan : array();
        $taxPurchase    = $this->variation->tax_purchase ?? '';

        return [
            "id"                        => $this->id,
            'transaction_id'            => $this->transaction_id,
            'variation_id'              => $this->variation_id,
            'product_id'                => $this->product_id,
            "name"                      => $this->variation->full_name ?? '',
            'qty'                       => (int)$this->unit_qty,
            'unit_price'                => (int)$this->purchase_price,
            'without_discount'          => (int)$this->without_discount,
            'purchase_price_inc_tax'    => (int)$this->purchase_price_inc_tax,
            'discount_amount'           => (int)$this->discount_amount,
            'discount'                  => (int)$this->discount_amount,
            'tax'                       => 0,
            'total_discount'            => 0,
            'discount_type'             => $this->discount_type,
            'total_tax'                 => 0,
            'tax_purchase'              => $taxPurchase == 'yes' ? true : false,
            'unit'                      => $this->unit->id ?? '',
            'unit_name'                 => $this->unit->name ?? '',
            'subtotal'                  => (int)$this->subtotal,
            'subunits'                  => $units
        ];
    }
}
