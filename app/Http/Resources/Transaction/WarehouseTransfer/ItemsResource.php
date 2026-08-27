<?php

namespace App\Http\Resources\Transaction\WarehouseTransfer;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemsResource extends JsonResource
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
            'name'              => $this->product->name ?? '',
            'variation'         => array(
                'id'                => $this->variation->id ?? '',
                'name'              => $this->variation->name ?? '',
            ),
            'variation_unit'    => array(
                'id'                => $this->variation->unit->id ?? '',
                'name'              => $this->variation->unit->name ?? '',
            ),
            'unit'              => array(
                'id'                => $this->unit->id ?? '',
                'name'              => $this->unit->name ?? '',
                'qty'               => (int)$this->unit_qty
            ),
            'qty'               => (int)$this->qty_adjustment,
            'price'             => (float)$this->purchase_price,
            'stock_in_system'   => (int)$this->stock_sistem,
            'actual_stock'      => (int)$this->actual_stock,
            'type_adjustment'   => $this->type_adjustment
        ];
    }
}
