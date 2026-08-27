<?php

namespace App\Http\Resources\Hrm;

use Illuminate\Http\Resources\Json\JsonResource;

class KabsonResource extends JsonResource
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
            'employee'      => $this->employee->user->name ?? '',
            'method'        => $this->method->name ?? '',
            'amount'        => (float)$this->amount,
            'id'            => $this->id,
        ];
    }
}
