<?php

namespace App\Http\Resources\Reports\Stocks;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $varname = $this->variation->name ?? null;

        if ($varname == 'no-name') {
            $varname = '';
        }

        return [
            'product'       => $this->product->name ?? '',
            'variation'     => $varname,
            'store'         => array(
                'name'          => $this->store->name ?? '',
                'id'            => $this->store->id ?? ''
            ),
            'warehouse'     => array(
                'id'            => $this->warehouse->id ?? null,
                'name'          => $this->warehouse->name ?? 'Utama'
            ),
            'stock'         => (int)$this->qty_available,
        ];
    }
}
