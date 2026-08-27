<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceResource extends JsonResource
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
            'priode'        => $this->priode,
            'amount'        => (float)$this->amount,
            'designation'   => array(
                'id'            => $this->designation->id ?? '',
                'name'          => $this->designation->name ?? ''
            ),
            'department'    => array(
                'id'            => $this->designation->department->id ?? '',
                'name'          => $this->designation->department->name ?? ''
            )
        ];
    }
}
