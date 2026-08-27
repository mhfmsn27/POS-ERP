<?php

namespace App\Http\Resources\Master;

use Illuminate\Http\Resources\Json\JsonResource;

class TermPaymentResource extends JsonResource
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
            'day'           => (int)$this->day,
            'discount'      => (int)$this->discount,
            'due_date'      => (int)$this->due_date,
            'note'          => $this->note,
            'default'       => $this->default == 'yes' ? true : false
        ];
    }
}
