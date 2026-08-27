<?php

namespace App\Http\Resources\Inventory\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationDetailResource extends JsonResource
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
            'show'              => true,
            'barcode'           => $this->barcode,
            'name'              => $this->name,
            'sku'               => $this->sku,
            'purchase_price'    => $this->first_price,
            'selling_price'     => (int)$this->selling_price,
            'include_tax'       => $this->taxrate > 0 ? false : true,
            'purchase_tax'      => (int)$this->purchase_tax,
            'tax'               => (int)$this->taxrate,
            'unit'              => $this->unit->id ?? '',
            'unit_name'         => $this->unit->name ?? '',
            'unit_sale'         => $this->unit_sell->id ?? null,
            'unit_sale_name'    => $this->unit_sell->name ?? '',
            'unit_purchase'     => $this->unitpo->id ?? null,
            'unit_purchase_name' => $this->unitpo->name ?? '',
            'grocery'           => (int)$this->grocery,
            'tax_sell'          => $this->tax_sell == 'yes' ? true : false,
            'tax_purchase'      => $this->tax_purchase == 'yes' ? true : false,
            'is_point'          => $this->get_point == 'active' ? true : false,
            'point'             => (int)$this->point,
            'stock'             => $this->first_stock,
            'rak'               => array(
                'id'                => $this->rak->id ?? '',
                'name'              => $this->rak->rak ?? ''
            ),
            'subunits'          => [], 
        ];
    }
}
