<?php

namespace App\Http\Resources\Reports\Supplier;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierDueItemResource extends JsonResource
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
            'due_date'      => $this->due_end ?? $this->date,
            'transaction'   => array(
                'id'            => $this->transaction->id ?? '',
                'ref_no'        => $this->transaction->ref_no ?? '',
            ),
        ];
    }
}
