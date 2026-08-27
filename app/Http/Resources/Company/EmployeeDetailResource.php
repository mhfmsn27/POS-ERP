<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDetailResource extends JsonResource
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
            'salary'        => (float)$this->salary,
            'address'       => $this->address,
            'user'          => array(
                'id'            => $this->user->id ?? '',
                'name'          => $this->user->name ?? ''
            ),
            'department'    => array(
                'id'            => $this->designation->department->id ?? '',
                'name'          => $this->designation->department->name ?? ''
            ),
            'designation'   => array(
                'id'            => $this->designation->id ?? '',
                'name'          => $this->designation->name ?? ''
            )
        ];
    }
}
