<?php

namespace App\Http\Resources\Account;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxTransactionResource extends JsonResource
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
            'ref_no'        => $this->transaction->ref_no ?? '',
            'tax_ref'       => $this->transaction->no_tax_ref->number ?? '',
            'date'          => $this->created_at->format('Y-m-d'),
            'amount'        => (float)$this->amount,
            'account'       => $this->account->name ?? '',
            'tax_paid'      => $this->tax_paid == 'paid' ? true : false,
            'tax_gunggung'  => $this->tax_gunggung == 'yes' ? true : false,
            'tax_status'    => $this->tax_status,
            'tax_type'      => $this->account->name ?? '',
            'customer'      => array(
                'id'            => $this->transaction->customer->id ?? '',
                'name'          => $this->transaction->customer->name ?? '',
                'npwp'          => $this->transaction->customer->npwp ?? '-',
            ),
            'supplier'      => array(
                'id'            => $this->transaction->supplier->id ?? '',
                'name'          => $this->transaction->supplier->name ?? ''
            ),
            'account'       => array(
                'id'            => $this->account->id ?? '',
                'name'          => $this->account->name ?? ''
            )
        ];
    }
}
