<?php

namespace App\Http\Resources\Transaction\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SellHistoryPriceResource extends JsonResource
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
            'price'         => (float)$this->unit_price_before_disc,
            'customer'      => $this->transaction->customer->name ?? '',
            'ref_no'        => $this->transaction->ref_no ?? '',
            'date'          => $this->transaction->transaction_date ?? '',
            'name'          => $this->variation->full_name ?? ''
        ];
    }
}
