<?php

namespace App\Http\Resources\Inventory\Products;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
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
            'name'              => $this->name,
            'image'             => asset($this->default_image),
            'is_variant'        => $this->type == 'single' ? false : true,
            'is_stock'          => $this->is_stock == 'yes' ? true : false,
            'is_unit'           => $this->is_unit == 'yes' ? true : false,
            'is_active'         => $this->is_active == 'yes' ? true : false,
            'is_account'        => $this->is_account == 'yes' ? true : false,
            'price_type'        => $this->price_type,
            'barcode_type'      => $this->barcode_type,
            'alert_qty'         => (int)$this->alert_quantity,
            'weight'            => (int)$this->weight,
            'description'       => $this->description,
            'category'          => array(
                'id'                => $this->category->id ?? '',
                'name'              => $this->category->name ?? ''
            ),
            'brand'             => array(
                'id'                => $this->brand->id ?? '',
                'name'              => $this->brand->name ?? ''
            ),
            'media'             => array()
        ];
    }
}
