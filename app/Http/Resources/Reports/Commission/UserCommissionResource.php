<?php

namespace App\Http\Resources\Reports\Commission;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCommissionResource extends JsonResource
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
            'name'          => $this->name,
            'percentase'    => (int)$this->commission_percentase,
            'transaction'  => (float)$this->total_transaction($request->start_date, $request->end_date),
            'commission'    => (float)$this->total_commission($request->start_date, $request->end_date)
        ];
    }
}
