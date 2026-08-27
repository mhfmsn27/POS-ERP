<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class RolePermissionResources extends JsonResource
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
            'role_id'       => $request->role->id ?? '',
            'permission_id' => $this->id,
            'name'          => $this->desc,
            'code'          => $this->name,
            'status'        => $this->role_permission_access($request->role->id ?? '')
        ];
    }
}
