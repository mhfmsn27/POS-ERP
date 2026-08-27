<?php

namespace App\Http\Resources\Transaction\Purchase\Faktur;

use Illuminate\Http\Resources\Json\JsonResource;

class FakturDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $payment_method         = 'cash';
        $saldo                  = 0;

        if ($this->method_id == null) {
            $payment_method     = 'saldo';
        }

        if ($this->supplier) {
            $saldo              = $this->supplier->total_saldo ?? 0;
        }
        return [
            'id'                => $this->id,
            'date'              => substr($this->transaction_date, 0, 10),
            'ref_no'            => $this->ref_no ?? '',
            'status'            => $this->status,
            'total_payment'     => (float)$this->final_total,
            'subtotal'          => (float)$this->faktur_detail()->sum('pay_amount'),
            'total_due'         => 0,
            'payment_method'    => $payment_method,
            'method'            => array(
                'id'                => $this->method->id ?? '',
                'name'              => $this->method->name ?? ''
            ),
            "store"             => array(
                'name'              => $this->store->name ?? '',
                'address'           => $this->store->address ?? '',
                'email'             => $this->store->email ?? '',
                'phone'             => $this->store->phone ?? ''
            ),
            "supplier"          => array(
                'id'                => $this->supplier->id ?? '',
                'name'              => $this->supplier->name ?? '',
                'address'           => $this->supplier->address ?? '',
                'email'             => $this->supplier->email ?? '',
                'phone'             => $this->supplier->phone ?? '',
                'total_saldo'       => (int)$saldo
            ),
            'created_date'      => array(
                'date'              => substr($this->created_at, 0, 10),
                'time'              => substr($this->created_at, 11, 16)
            ),
            'created'           => array(
                'name'              => $this->createdby->name ?? ''
            ),
            'fakturs'          => FakturItemResource::collection($this->faktur_detail)
        ];
    }
}
