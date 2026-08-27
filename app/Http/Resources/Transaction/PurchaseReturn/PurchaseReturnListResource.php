<?php

namespace App\Http\Resources\Transaction\PurchaseReturn;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReturnListResource extends JsonResource
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
            'ref_no'            => $this->ref_no,
            'qty'               => (int)$this->qty_return,
            'due_total_return'  => (float)$this->due_total_return,
            'status_payment'    => $this->payment_status,
            'final_total'       => (float)$this->final_total,
            'date'              => substr($this->transaction_date, 0, 10),
            'transaction'       => array(
                'id'                => $this->transaction->id ?? '',
                'ref_no'            => $this->transaction->ref_no ?? '',
                'date'              => substr($this->transaction->transaction_date, 0, 10)
            ),
            'store'             => array(
                'id'                => $this->store->id ?? '',
                'name'              => $this->store->name ?? '',
                'logo'              => asset($this->store->image_data ?? 'uploads/image-default.jpeg')
            ),
            'supplier'          => array(
                'id'                => $this->supplier->id ?? '',
                'name'              => $this->supplier->name ?? '',
                'photo'             => asset($this->supplier->image_data ?? 'uploads/image-default.jpeg')
            ),
            'created'           => array(
                'id'                => $this->createdby->id ?? '',
                'name'              => $this->createdby->name ?? ''
            )
        ];
    }
}
