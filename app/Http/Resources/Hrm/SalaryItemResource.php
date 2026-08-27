<?php

namespace App\Http\Resources\Hrm;

use Illuminate\Http\Resources\Json\JsonResource;

class SalaryItemResource extends JsonResource
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
            'name'      => $this->name,
            'type'      => $this->type,
            'amount'    => (int)$this->amount,
            'qty'       => $this->qty,
            'subtotal'  => (int)$this->subtotal
        ];
    }
}
