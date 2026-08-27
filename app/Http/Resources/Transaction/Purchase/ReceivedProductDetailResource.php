<?php

namespace App\Http\Resources\Transaction\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;

class ReceivedProductDetailResource extends JsonResource
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
            "supplier"          => array(
                'id'                => $this->supplier->id ?? '',
                'name'              => $this->supplier->name ?? '',
                'address'           => $this->supplier->address ?? '',
                'email'             => $this->supplier->email ?? '',
                'phone'             => $this->supplier->phone ?? '',
                'default'           => ($this->supplier->tax_default ?? '') == 'yes' ? true : false,
                'tax_option'        => ($this->supplier->tax_option ?? '') == 'yes' ? true : false,
                'customer_type'     => $this->supplier->type ?? '',
                'due_date'          => ($this->supplier->term)  ? (int)$this->supplier->term->due_date : 0
            ),
            'warehouse'     => array(
                'id'            => $this->warehouse->id ?? '',
                'name'          => $this->warehouse->name ?? 'Gudang Utama'
            ),
            "date"              => substr($this->transaction_date, 0, 10),
            "ref_no"            => $this->ref_no,
            'supplier_ref'      => $this->supplier_ref,
            'note'              => $this->additional_notes,
            'address'           => $this->address,
            'created_date'      => array(
                'date'              => substr($this->created_at, 0, 10),
                'time'              => substr($this->created_at, 11, 16)
            ),
            'created'           => array(
                'name'              => $this->createdby->name ?? ''
            ),
            "items"             => ReceivedProductItemResource::collection($this->purchase_received),
            "summary"           => array(
                'subtotal'          => (int)$this->total_before_tax,
                'discount'          => 0,
                'tax'               => 0,
                'total'             => (int)$this->final_total
            )
        ];
    }
}
