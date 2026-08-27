<?php

namespace App\Http\Resources\Transaction\Purchase\Po;

use Illuminate\Http\Resources\Json\JsonResource;

class PoListResource extends JsonResource
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
            'supplier_ref'      => $this->supplier_ref,
            'status'            => $this->status, 
            'final_total'       => (float)$this->final_total, 
            'note'              => $this->additional_notes,
            'store'             => array(
                'id'                => $this->store->id ?? '',
                'name'              => $this->store->name ?? '',
            ),
            'supplier'          => array(
                'id'                => $this->supplier->id ?? '',
                'name'              => $this->supplier->name ?? '',
            ),
            'created'           => array(
                'id'                => $this->createdby->id ?? '',
                'name'              => $this->createdby->name ?? ''
            ),
        ];
    }
}
