<?php

namespace App\Http\Resources\Reports\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDueItemResource extends JsonResource
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
            'date'          => substr($this->date,0,10),
            'amount'        => (float)$this->amount,
            'total_pay'     => (float)$this->total_payment,
            'total_due'     => (float)$this->total_due,
            'umur'          => $this->umur,
            'due_date'      => substr($this->due_end,0,10),
            'transaction'   => array(
                'id'            => $this->transaction->id ?? '',
                'ref_no'        => $this->transaction->ref_no ?? '',
            ),
        ];
    }
}
