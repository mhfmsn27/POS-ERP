<?php

namespace App\Http\Resources\Administrator\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
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
            'business'          => array(
                'name'              => $this->store->merchant->business_name ?? '',
                'address'           => $this->store->merchant->address ?? '',
                'email'             => $this->store->merchant->owner->email ?? '',
                'phone'             => $this->store->merchant->owner->phone ?? ''
            ),
            'store'             => array(
                'name'              => $this->store->name ?? '',
                'phone'             => $this->store->phone ?? '',
                'email'             => $this->store->email ?? ''
            ),
            'package'           => array(
                'name'              => $this->package->name ?? '',
                'duration'          => $this->package ? (int)$this->package->limit_day : 0,
                'price'             => number_format($this->subtotal)
            ),
            'created'           => $this->created_at->format("Y-m-d"),
            'status'            => $this->status, 
            'tax'               => number_format($this->tax),
            'subtotal'          => number_format($this->subtotal),
            'grand_total'       => number_format($this->grand_total)
        ];
    }
}
