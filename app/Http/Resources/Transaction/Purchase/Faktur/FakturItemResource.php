<?php

namespace App\Http\Resources\Transaction\Purchase\Faktur;

use Illuminate\Http\Resources\Json\JsonResource;

class FakturItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if ($this->transaction->status == 'final') {
            $totalDue       = $this->transaction_due->total_due + $this->pay_amount;
        } else {
            $totalDue       = $this->transaction_due->total_due;
        }

        return [
            'transaction_id'    => $this->transaction_due->transaction->id ?? null,
            'item_id'           => $this->id,
            'id'                => $this->transaction_due->id ?? '',
            'ref_no'            => $this->transaction_due->no_ref ?? '',
            'date'              => $this->transaction_due->date ?? '',

            'amount'            => (float)$this->transaction_due->amount ?? 0,
            'total_pay'         => (float)$this->transaction_due->total_pay ?? 0,
            'total_due'         => (float)$totalDue,
            'pay'               => (float)$this->pay_amount,
            'type'              => $this->transaction_due->type ?? 'hutang'

        ];
    }
}
