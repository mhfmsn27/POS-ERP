<?php

namespace App\Http\Resources\Hrm;

use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
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
            'id'                    => $this->id,
            'user'                  => $this->user->name ?? '',
            'commission_total'      => (float)$this->commission_total,
            'faktur'                => $this->transaction->ref_no ?? '',
            'created'               => $this->transaction->createdby->name ?? '',
            'status'                => $this->status,
            'final_total'           => (float)$this->transaction->final_total,
            'date'                  => $this->created_at->format('Y-m-d')
        ];
    }
}
