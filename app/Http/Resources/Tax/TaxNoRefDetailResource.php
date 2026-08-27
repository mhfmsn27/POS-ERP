<?php

namespace App\Http\Resources\Tax;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxNoRefDetailResource extends JsonResource
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
            'number'        => $this->number,
            'transaction'   => array(
                'id'            => $this->transaction->id ?? null,
                'ref_no'        => $this->transaction->ref_no ?? null,
                'date'          => $this->transaction->transaction_date ?? ''
            )
        ];
    }
}
