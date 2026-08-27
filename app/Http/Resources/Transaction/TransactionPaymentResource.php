<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionPaymentResource extends JsonResource
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
            'id'                => $this->id,
            'due_id'            => $this->transaction_due_id,
            'amount'            => (int)$this->amount,
            'date'              => $this->date,
            'note'              => $this->note,
            'method_name'       => $this->method,
            'method'            => array(
                'id'                => $this->payment_method->id ?? '',
                'name'              => $this->payment_method->name ?? ''
            ),
            'created'           => $this->user->name ?? ''
        ];
    }
}
