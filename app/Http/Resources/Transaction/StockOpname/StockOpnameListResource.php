<?php

namespace App\Http\Resources\Transaction\StockOpname;

use Illuminate\Http\Resources\Json\JsonResource;

class StockOpnameListResource extends JsonResource
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
            'date'          => $this->transaction_date,
            'ref_no'        => $this->ref_no,
            'store'         => array(
                'id'            => $this->store->id ?? '',
                'name'          => $this->store->name ?? ''
            ),
            'created'       => array(
                'id'            => $this->createdby->id ?? '',
                'name'          => $this->createdby->name ?? ''
            ),
            'note'          => $this->additional_notes
        ];
    }
}
