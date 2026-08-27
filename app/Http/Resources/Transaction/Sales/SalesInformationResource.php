<?php

namespace App\Http\Resources\Transaction\Sales;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesInformationResource extends JsonResource
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
            'no_tax'        => $this->no_tax_ref->number ?? null,
            'type'          => $this->type,
            'note'          => $this->additional_notes ?? '',
            'customer'      => array(
                'id'            => $this->customer->id ?? null,
                'name'          => $this->customer->name ?? '',
                'phone'         => $this->customer->phone ?? '',
                'email'         => $this->customer->email ?? '',
                'address'       => $this->customer->address ?? '',
                'default'       => $this->customer->tax_default == 'yes' ? true : false,
                'type'          => $this->customer->type ?? 'general',
                'tax_option'    => $this->customer->tax_option == 'yes' ? true : false,
                'npwp'          => $this->customer->npwp ?? null,
            ),
            'user'          => array(
                'id'            => $this->commission_user->id ?? '',
                'name'          => $this->commission_user->name ?? ''
            ),
            'store'         => array(
                'id'            => $this->store->id ?? '',
                'name'          => $this->store->name ?? '',
                'address'       => $this->store->address ?? '',
                'phone'         => $this->store->phone ?? '',
                'email'         => $this->store->email ?? '',
                'footer_text'   => $this->store->footer_text ?? ''
            ),
            'warehouse'     => array(
                'id'            => $this->warehouse->id ?? '',
                'name'          => $this->warehouse->name ?? 'Gudang Utama'
            ),
            'courier'       => array(
                'id'            => $this->courier->id ?? '',
                'name'          => $this->courier->name ?? ''
            ),
            'address'       => $this->address,
            'date'          => substr($this->transaction_date, 0, 10),
            'created_date'  => array(
                'date'          => $this->created_at->format('Y-m-d'),
                'time'          => $this->created_at->format('H:i:s')
            ),
            'created'       => array(
                'id'            => $this->createdby->id ?? '',
                'name'          => $this->createdby->name ?? ''
            ),
            'no_ref'        => $this->ref_no,
            'status'        => $this->status,
            'payment_status' => $this->payment_status,
            'due_limit'     => (int)$this->due_limit
        ];
    }
}
