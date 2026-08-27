<?php

namespace App\Http\Resources\Account;

use Illuminate\Http\Resources\Json\JsonResource;

class SptListResource extends JsonResource
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
            'date'      => array(
                'start'     => $this->start_date,
                'end'       => $this->end_date,
            ),
            'ntpt'      => $this->ntpt,
            'amount'    => (float)$this->amount,
            'type'      => $this->type,
            'created'   => $this->created_at->format('Y-m-d')
        ];
    }
}
