<?php

namespace App\Http\Resources\Transaction\Sales\Faktur;

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

        if ($this->customer) {
            $saldo              = $this->customer->total_saldo ?? 0;
        }

        return [
            'id'                => $this->id,
            'date'              => substr($this->transaction_date, 0, 10),
            'ref_no'            => $this->ref_no,
            'status'            => $this->status,
            'total_payment'     => (float)$this->final_total_faktur,
            'total_credit'      => abs((float)$this->faktur_detail()->sum('pay_amount')),
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
            "customer"          => array(
                'id'                => $this->customer->id ?? '',
                'name'              => $this->customer->name ?? '',
                'address'           => $this->customer->address ?? '',
                'email'             => $this->customer->email ?? '',
                'phone'             => $this->customer->phone ?? '',
                'total_saldo'       => (float)$saldo
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
