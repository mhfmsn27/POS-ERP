<?php

namespace App\Http\Resources\Account;

use Illuminate\Http\Resources\Json\JsonResource;

class SptDetailResource extends JsonResource
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
            'start_date'        => $this->start_date,
            'end_date'          => $this->end_date,
            'ntpt'              => $this->ntpt,
            'payment_date'      => substr($this->payment_date, 0, 10),
            'amount'            => (float)$this->amount,
            'type'              => $this->type,
            'note'              => $this->note,
            'items'             => SptItemResource::collection($this->detail)
        ];
    }
}
