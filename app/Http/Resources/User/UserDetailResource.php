<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            'stores'                        => store_array(explode(",", $this->store_id)),
            'commission'                    => (int)$this->commission_percentase, 
            'max_commission'                => (float)$this->max_commission,
            'name'                          => $this->name,
            'email'                         => $this->email,
            'phone'                         => $this->phone, 
            'role'                          => $this->role, 
            'jk'                            => $this->jk, 
        ];
    }
}
