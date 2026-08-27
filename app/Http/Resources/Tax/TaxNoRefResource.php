<?php

namespace App\Http\Resources\Tax;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxNoRefResource extends JsonResource
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
            'id'        => $this->id,
            'from'      => $this->from_number,
            'to'        => $this->to_number,
            'terpakai'  => (int)$this->details()->where("transaction_id", "!=", null)->count(),
            'total'     => (int)$this->details()->count(),
            'status'    => $this->status_data,
            'type'      => $this->type
        ];
    }
}
