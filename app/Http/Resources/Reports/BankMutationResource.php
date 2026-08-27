<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Resources\Json\JsonResource;

class BankMutationResource extends JsonResource
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
            'date'      => $this->operation_date,
            'ref_no'    => $this->ref_no,
            'name'      => $this->name,
            'rekon'     => $this->after_rekonsiliasi == 'yes' ? true : false,
            'debit'     => $this->type == 'debit' ? (float)$this->amount : 0,
            'credit'    => $this->type == 'credit' ? (float)$this->amount : 0,
            'logs'      => $this->last_log ? (float)$this->last_log->amount : 0
        ];
    }
}
