<?php

namespace App\Http\Resources\Account\Jurnal;

use Illuminate\Http\Resources\Json\JsonResource;

class JurnalListDetailResource extends JsonResource
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
            'name'          => $this->additional_notes,
            'date'          => substr($this->transaction_date, 0, 10),
            'items'         => JurnalItemResource::collection($this->jurnal)
        ];
    }
}
