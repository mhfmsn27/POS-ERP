<?php

namespace App\Http\Resources\Inventory;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'data'      => array(
                'id'                => $this->id,
                'name'              => $this->name,
                'code'              => $this->code,
                'value'             => (int)$this->value,
                'price'             => (int)$this->price,
                'is_root_parent'    => $this->is_root_parent == 1 ? true : false,
                'operator'          => $this->operator,
                'parent'            => array(
                    'id'                => $this->parent->id ?? '',
                    'name'              => $this->parent->name ?? ''
                ),
            ),
            'children'          => UnitResource::collection($this->unitdown)
        ];
    }
}
