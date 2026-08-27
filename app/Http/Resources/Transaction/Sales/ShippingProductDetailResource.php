<?php

namespace App\Http\Resources\Transaction\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingProductDetailResource extends JsonResource
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
            'status'            => $this->status,
            "store"             => array(
                'name'              => $this->store->name ?? '',
                'address'           => $this->store->address ?? '',
                'email'             => $this->store->email ?? '',
                'phone'             => $this->store->phone ?? ''
            ),
            "customer"          => array(
                'id'                => $this->customer->id ?? '',
                'name'              => $this->customer->name ?? '',
                'address'           => $this->store->address ?? '',
                'email'             => $this->store->email ?? '',
                'phone'             => $this->store->phone ?? ''
            ),
            'warehouse'         => array(
                'id'                => $this->warehouse->id ?? '',
                'name'              => $this->warehouse->name ?? 'Gudang Utama'
            ),
            'courier'           => array(
                'id'                => $this->courier->id ?? '',
                'name'              => $this->courier->name ?? ''
            ),
            'address'           => $this->address ?? '',
            "date"              => substr($this->transaction_date, 0, 10),
            "ref_no"            => $this->ref_no, 
            'note'              => $this->additional_notes,
            'created_date'      => array(
                'date'              => substr($this->created_at, 0, 10),
                'time'              => substr($this->created_at, 11, 16)
            ),
            'created'           => array(
                'name'              => $this->createdby->name ?? ''
            ),
            "items"             => ShippingProductItemResource::collection($this->sale_shipping),
            "summary"           => array(
                'subtotal'          => (int)$this->total_before_tax,
                'discount'          => 0,
                'tax'               => 0,
                'total'             => (int)$this->final_total
            )
        ];
    }
}
