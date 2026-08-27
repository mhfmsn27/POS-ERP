<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDueResource extends JsonResource
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
            'ref_no'        => $this->no_ref,
            'supplier_ref'  => $this->transaction->ref_no ?? '',
            'date'          => substr($this->date, 0, 10),
            'amount'        => (float)$this->amount,
            'total_pay'     => (float)$this->total_payment,
            'total_due'     => (float)$this->total_due,
            'type'          => $this->type,
            'status'        => $this->status,
            'transaction'   => array(
                'id'            => $this->transaction->id ?? '',
                'ref_no'        => $this->transaction->ref_no ?? '',
            ),
            'supplier'      => array(
                'id'            => $this->supplier->id ?? '',
                'name'          => $this->supplier->name ?? ''
            ),
            'customer'      => array(
                'id'            => $this->customer->id ?? '',
                'name'          => $this->customer->name ?? ''
            )
        ];
    }
}
