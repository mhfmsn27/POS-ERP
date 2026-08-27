<?php

namespace App\Http\Resources\Transaction\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseInformationResource extends JsonResource
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
            'supplier'      => array(
                'id'            => $this->supplier->id ?? null,
                'name'          => $this->supplier->name ?? '',
                'country'       => $this->supplier->country->name ?? '',
                'phone'         => $this->supplier->phone ?? '',
                'email'         => $this->supplier->email ?? '',
                'address'       => $this->supplier->address ?? '',
                'default'       => $this->supplier->tax_default == 'yes' ? true : false,
            ),
            'store'         => array(
                'id'            => $this->store->id ?? '',
                'name'          => $this->store->name ?? '',
                'address'       => $this->store->address ?? '',
                'phone'         => $this->store->phone ?? '',
                'email'         => $this->store->email ?? ''
            ),
            'warehouse'     => array(
                'id'            => $this->warehouse->id ?? '',
                'name'          => $this->warehouse->name ?? 'Gudang Utama'
            ),
            'date'          => substr($this->transaction_date, 0, 10),
            'created_date'  => array(
                'date'          => $this->created_at->format('Y-m-d'),
                'time'          => $this->created_at->format('H:i:s')
            ),
            'created'       => array(
                'id'            => $this->createdby->id ?? '',
                'name'          => $this->createdby->name ?? ''
            ),
            'address'       => $this->address,
            'no_ref'        => $this->ref_no,
            'status'        => $this->status,
            'payment_status' => $this->payment_status,
            'supplier_ref'  => $this->supplier_ref,
            'due_limit'     => (int)$this->due_limit
        ];
    }
}
