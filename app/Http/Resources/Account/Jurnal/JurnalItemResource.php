<?php

namespace App\Http\Resources\Account\Jurnal;

use Illuminate\Http\Resources\Json\JsonResource;

class JurnalItemResource extends JsonResource
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
            'type'          => $this->type,
            'amount_credit' => $this->type == 'debit' ? 0 : (float)$this->amount,
            'amount'        => $this->type == 'debit' ? (float)$this->amount : 0,
        ];
    }
}
