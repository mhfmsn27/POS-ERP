<?php

namespace App\Http\Resources\Hrm;

use Illuminate\Http\Resources\Json\JsonResource;

class SalaryListResource extends JsonResource
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
            'name'          => $this->employee->user->name ?? '',
            'store'         => $this->store->name ?? '',
            'date'          => $this->created_at,
            'status'        => $this->status,
            'cutting'       => (float)$this->cutting,
            'allowance'     => (float)$this->allowance,
            'bonus'         => (float)$this->bonus,
            'tax'           => (float)$this->tax,
            'salary'        => (float)$this->salary,
            'kasbon'        => (float)$this->kasbon,
            'total'         => (float)$this->total
        ];
    }
}
