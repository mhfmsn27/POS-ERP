<?php

namespace App\Http\Resources\Rma;

use Illuminate\Http\Resources\Json\JsonResource;

class RmaDetailResource extends JsonResource
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
            'status'        => $this->status,
            'ref_no'        => $this->ref_no,
            'created_date'  => array(
                'date'          => $this->created_at->format('Y-m-d'),
                'time'          => $this->created_at->format('H:i')
            ),
            'complete_date' => $this->complete_date,
            'customer_name' => $this->customer_name,
            'phone'         => $this->phone,
            "customer"          => array(
                'id'                => $this->customer->id,
                'name'              => $this->customer_name ?? ($this->customer->name ?? ''),
                'address'           => $this->customer->address ?? '',
                'email'             => $this->customer->email ?? '',
                'phone'             => $this->phone ?? ($this->customer->phone ?? '')
            ),
            "store"             => array(
                'name'              => $this->store->name ?? '',
                'address'           => $this->store->address ?? '',
                'email'             => $this->store->email ?? '',
                'phone'             => $this->store->phone ?? ''
            ),

            'note'          => $this->note,
            'estimate_date' => $this->estimate_date,
            'estimate_price'    => (float)$this->estimate_price,
            'items'         => RmaItemsResource::collection($this->details)
        ];
    }
}
