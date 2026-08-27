<?php

namespace App\Http\Resources\Rma;

use Illuminate\Http\Resources\Json\JsonResource;

class RmaListResource extends JsonResource
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
            'customer'      => $this->customer_name ?? ($this->customer->name ?? ''),
            'items'         => $this->details->count(),
            'note'          => $this->note,
            'ref_no'        => $this->ref_no,
            'date'          => $this->created_at->format('Y-m-d'),
            'status'        => $this->status
        ];
    }
}
