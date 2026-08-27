<?php

namespace App\Http\Resources\Inventory\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class VariatioDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $units      = array();
        $varname    = $this->name == 'no-name' ? '' : $this->name;
        $units      = $this->unit != null ? $this->unit->unit_turunan : array();
 
        return [
            'id'                => $this->id,
            'product_id'        => $this->product_id,
            'barcode'           => $this->barcode,
            'image'             => asset($this->product->default_image ?? 'assets/uploads/image.jpg'),
            'name'              => $this->product->name . ' ' . $varname,
            'sku'               => $this->sku,
            'type'              => $this->product->is_stock == 'yes' ? true : false,
            'purchase_price'    => $this->modal_price,
            'selling_price'     => (int)$this->selling_price,
            'stock'             => (int)$this->stock->sum("qty_available"),
            'stock_adjustment'  => isset($request->warehouse) ? (int)$this->stock_by_warehouse($request->warehouse)->sum('qty_available') : (int)$this->stock->sum("qty_available"),
            'unit_name'         => $this->unit->name ?? '',
            'unit'              => $this->unit->id ?? '',
            'unit_sale'         => $this->unit_sell->id ?? '',
            'unit_purchase'     => $this->unitpo->id ?? '',
            'grocery'           => (int)$this->grocery,
            'tax_sell'          => $this->tax_sell == 'yes' ? true : false,
            'tax_purchase'      => $this->tax_purchase == 'yes' ? true : false,
            'point'             => (int)$this->point,
            'units'             => $units,
        ];
    }
}
