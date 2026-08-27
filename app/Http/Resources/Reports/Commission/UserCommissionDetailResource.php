<?php

namespace App\Http\Resources\Reports\Commission;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCommissionDetailResource extends JsonResource
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
            'ref_no'        => $this->transaction->ref_no ?? '',
            'customer'      => $this->transaction->customer->name ?? '',
            'date'          => $this->transaction->transaction_date ?? '',
            'commission'    => (float)$this->commission_total,
            'created'       => $this->transaction->createdby->name ?? '',
            'faktur'        => $this->transaction ? (float)$this->transaction->final_total : 0
        ];
    }
}
