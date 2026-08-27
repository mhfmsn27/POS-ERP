<?php

namespace App\Http\Resources\Accout;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountHistoryResource extends JsonResource
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
            'id'        => $this->id,
            'date'      => date_format_indo($this->operation_date),
            'tanggal'   => substr($this->operation_date, 0, 10),
            'ref_no'    => $this->ref_no,
            'name'      => $this->name,
            'sub_type'  => $this->sub_type,
            'type'      => $this->type,
            'amount'    => (int)$this->amount,
            'transaction'   => array(
                'id'            => $this->transaction->id ?? null,
                'ref'           => $this->transaction->ref_no ?? null,
                'type'          => $this->transaction->type ?? '',
                'route'         => $this->route_name
            ),
            'customer'      => array(
                'name'          => $this->transaction->customer->name ?? '',
            ),
            'supplier'      => array(
                'name'          => $this->transaction->supplier->name ?? ''
            ),
            'transaction_due'   => array(
                'id'            => $this->transaction_due->id ?? null,
                'ref'           => $this->transaction_due->no_ref ?? null,
            ),
            'saldo'     => number_format($this->cashflow),
            'note'      => $this->note
        ];
    }
}
