<?php

namespace App\Http\Resources\Inventory\Products;

use App\Models\Admin\Warehouse;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $listStockVariations    = [];
        foreach (Warehouse::all() as $warehouse) {

            $wr['id']           = $warehouse->id;
            $wrVariations       = [];
            foreach ($this->variant as $variant) { 
                $i['id']                = $variant->id;
                $i['stock']             = (float)$variant->stock_by_warehouse($warehouse->id)->sum('qty_available');
                
                $wrVariations[]         = $i;
            }

            $wr['variations']           = $wrVariations;
            $listStockVariations[]      = $wr;
        }

        return [
            'id'                        => $this->id,
            'name'                      => substr($this->name,0,50),
            'type'                      => $this->type,
            'category'                  => $this->category->name ?? '',
            'brand'                     => $this->brand->name ?? '',
            'image'                     => asset($this->default_image),
            'is_stock'                  => $this->is_stock == 'yes' ? true : false,
            'warehouses'                => $listStockVariations,
            'variants'                  => ProductVariationListResource::collection($this->variant),
        ];
    }
}
