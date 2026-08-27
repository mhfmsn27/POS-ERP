<?php

namespace App\Http\Resources\Reports\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSaldoItemResource extends JsonResource
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
            'date'          => $this->date,
            'amount'        => (float)$this->amount,
            'total_pay'     => (float)$this->total_payment,
            'total_due'     => (float)$this->total_due,
            'umur'          => $this->umur,
            'transaction'   => array(
                'id'            => $this->transaction->id ?? '',
                'ref_no'        => $this->transaction->ref_no ?? '',
            ),
        ];
    }
}
