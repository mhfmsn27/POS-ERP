<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTransactionResource extends JsonResource
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
            'amount'        => (int)$this->amount,
            'note'          => $this->note, 
            'date'          => $this->created_at->format("Y-m-d H:i:s"),
            'status'        => $this->payment_status, 
            'transaction'   => array(
                'id'            => $this->transaction->id ?? '',
                'no_ref'        => $this->transaction->ref_no ?? '',
            ), 
            'method'        => array(
                'id'            => $this->payment_method_id,
                'name'          => $this->method,
                'from_bank'     => $this->bank_name,
                'to_bank'       => $this->to_bank,
                'file'          => asset($this->file),
                'icon'          => $this->methode ? asset($this->methode->image_data ?? '') : null,
            ),
            'createdby'     => array(
                'id'            => $this->user->id ?? '',
                'name'          => $this->user->name ?? ''
            ),
            'account'       => array(
                'id'            => $this->account->account_id ?? ''
            )
        ];
    }
}
