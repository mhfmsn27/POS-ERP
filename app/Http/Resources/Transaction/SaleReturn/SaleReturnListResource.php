<?php

namespace App\Http\Resources\Transaction\SaleReturn;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleReturnListResource extends JsonResource
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
            'ref_no'        => $this->ref_no,
            'date'          => substr($this->transaction_date, 0, 10),
            'final_total'   => (float)$this->final_total,
            'status'        => $this->payment_status,
            'due_total'     => (float)$this->due_total_return_sell, 
            'store'         => array(
                'id'            => $this->store->id ?? '',
                'name'          => $this->store->name ?? ''
            ),
            'created'           => array(
                'id'                => $this->createdby->id ?? '',
                'name'              => $this->createdby->name ?? ''
            ),
            'customer'      => array(
                'id'            => $this->customer->id ?? '',
                'name'          => $this->customer->name ?? ''
            ),
        ];
    }
}
