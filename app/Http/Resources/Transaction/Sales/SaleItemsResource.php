<?php

namespace App\Http\Resources\Transaction\Sales;

use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Uuid;

class SaleItemsResource extends JsonResource
{
    /**
     * Transform the resource floato an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    { 
        return [
            'id'                    => Uuid::uuid4()->toString(),
            'item_position'         => $this->item_position,
            'item_id'               => $this->id,
            'variation_id'          => $this->variation_id,
            'product_id'            => $this->product_id,
            'name'                  => $this->item_name ?? ($this->variation->full_name ?? ''),
            'qty'                   => (float)$this->unit_qty,
            'unit_price'            => (float)$this->unit_price,
            'discount_amount'       => (float)$this->disc_amount,
            'total_discount'        => (float)$this->total_discount,
            'total_tax'             => (float)$this->tax_total,
            'service_tax'           => (float)$this->service_tax,
            'goverment_tax'         => (float)$this->goverment_tax,
            'product_type'          => $this->product->is_stock == 'yes' ? true : false,
            'tax'                   => (float)$this->item_tax,
            'purchase_price'        => (float)$this->variation->modal_price,
            'without_discount'      => (float)$this->unit_price_before_disc,
            'unit_price_inc_tax'    => (float)$this->unit_price,  
            'discount_subtotal'     => (float)$this->discount_subtotal,
            'discount_type'         => $this->discount_type,
            'unit'                  => $this->unit_id,
            'subtotal'              => (float)($this->qty * ((float)($this->unit_price))),
            'subunits'              => $this->variation->unit != null ? $this->variation->unit->unit_turunan : array(),
            'unit_detail'           => array(
                'id'                    => $this->unit->id ?? '',
                'name'                  => $this->unit->name ?? '',
                'price'                 => $this->price_floato_unit,
            ),
            'first_unit'            => array(
                'name'                  => $this->variation->unit->name ?? '',
                'id'                    => $this->variation->unit->id ?? null,
            ),
            'qty_no_unit'           => (float)$this->qty,
            'qty_detail'            => array(
                'qty_return'            => (float)$this->qty_return,
                'can_return'            => (float)$this->qty_can_return,
                'qty'                   => (float)$this->qty
            )
        ];
    }
}
