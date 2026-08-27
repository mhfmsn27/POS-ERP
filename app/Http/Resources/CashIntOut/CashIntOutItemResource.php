<?php

namespace App\Http\Resources\CashIntOut;

use Illuminate\Http\Resources\Json\JsonResource;

class CashIntOutItemResource extends JsonResource
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
            'account_id'    => $this->account_id,
            'coa'           => $this->account->coa ?? '',
            'name'          => $this->name,
            'amount'        => (float)$this->amount
        ];
    }
}
