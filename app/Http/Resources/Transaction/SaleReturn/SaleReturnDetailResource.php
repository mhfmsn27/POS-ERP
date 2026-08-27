<?php

namespace App\Http\Resources\Transaction\SaleReturn;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReturnDetailResource extends JsonResource
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
            'date'              => substr($this->transaction_date, 0, 10),
            'status'            => $this->status,
            'subtotal'          => $this->subtotal_purchase_return,
            'tax_percent'       => (int)$this->tax_amount,
            'tax_amount'        => (float)$this->tax_final,
            'final_total'       => (float)$this->final_total,
            'payment_total'     => (float)$this->payment()->sum("amount"),
            'payment_status'    => $this->payment_status, 
            'discount'          => array(
                'type'              => $this->discount_type,
                'amount'            => (float)$this->discount_amount,
                'total'             => (float)$this->discount_final
            ),
            'transaction'       => array(
                'id'                => $this->transaction->id ?? '',
                'ref_no'            => $this->transaction->ref_no ?? ''
            ),
            'store'             => array(
                'id'                => $this->store->id ?? '',
                'name'              => $this->store->name ?? '',
                'address'           => $this->store->address ?? '',
                'phone'             => $this->store->phone ?? '',
                'email'             => $this->store->email ?? ''
            ),
            'customer'          => array(
                'id'                => $this->customer->id ?? null,
                'name'              => $this->customer->name ?? '', 
                'phone'             => $this->customer->phone ?? '',
                'email'             => $this->customer->email ?? '',
                'address'           => $this->customer->address ?? ''
            ),
            'created'           => array(
                'id'                => $this->createdby->id ?? '',
                'name'              => $this->createdby->name ?? ''
            ),
            'created_date'      => array(
                'date'              => $this->created_at->format('Y-m-d'),
                'time'              => $this->created_at->format('H:i:s')
            ),
            'transaction'       => array(
                'id'                => $this->transaction->id ?? '',
                'ref_no'            => $this->transaction->ref_no ?? ''
            ),
           
            'items'             => SaleReturnItemResource::collection($this->sellreturn), 
        ];
    }
}
