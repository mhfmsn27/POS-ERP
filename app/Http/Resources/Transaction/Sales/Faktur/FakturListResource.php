<?php

namespace App\Http\Resources\Transaction\Sales\Faktur;

use Illuminate\Http\Resources\Json\JsonResource;

class FakturListResource extends JsonResource
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
            'date'              => substr($this->transaction_date, 0, 10),
            'ref_no'            => $this->ref_no,
            'status'            => $this->status,
            'subtotal'          => (float)$this->faktur_detail()->sum('pay_amount'),
            'final_total'       => (float)$this->final_total_faktur,
            'method'            => array(
                'id'                => $this->method->id ?? '',
                'name'              => $this->method->name ?? ''
            ),
            'store'             => array(
                'id'                => $this->store->id ?? '',
                'name'              => $this->store->name ?? '',
            ),
            'customer'          => array(
                'id'                => $this->customer->id ?? '',
                'name'              => $this->customer->name ?? '',
            ),
            'created'           => array(
                'id'                => $this->createdby->id ?? '',
                'name'              => $this->createdby->name ?? ''
            ),
        ];
    }
}
