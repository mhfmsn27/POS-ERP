<?php

namespace App\Http\Resources\Hrm;

use Illuminate\Http\Resources\Json\JsonResource;

class KabsonDetailResource extends JsonResource
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
            'note'          => $this->note,
            'amount'        => (float)$this->amount,
            'type'          => $this->type,
            'date'          => $this->created_at->format("Y-m-d"),
            'employee'      => array(
                'id'            => $this->employee->id,
                'name'          => $this->employee->user->name ?? ''
            ),
            'method'        => array(
                'id'            => $this->method->id ?? '',
                'name'          => $this->method->name ?? ''
            ),
        ];
    }
}
