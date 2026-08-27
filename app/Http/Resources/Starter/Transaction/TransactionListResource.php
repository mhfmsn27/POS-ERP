<?php

namespace App\Http\Resources\Starter\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionListResource extends JsonResource
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
            'id'            => $this->id,
            'date'          => $this->created_at->format('Y-m-d'),
            'store'         => $this->store->name ?? '',
            'package'       => $this->package->name ?? '',
            'grand_total'   => (float)$this->grand_total,
            'status'        => $this->status
        ];
    }
}
