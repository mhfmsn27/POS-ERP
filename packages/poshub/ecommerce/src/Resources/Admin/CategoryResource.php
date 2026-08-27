<?php

namespace Poshub\Ecommerce\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            "data"          => array(
                'id'                    => $this->id,
                'name'                  => $this->name,
                'featured_category'     => $this->featured_category == 'yes' ? true : false,
                'is_root_parent'        => $this->is_root_parent == 1 ? true : false,
                'parent'                => array(
                    'id'                    => $this->parent->id ?? '',
                    'name'                  => $this->parent->name ?? ''
                ),
                'detail'                => $this->detail,
                'image'                 => asset($this->image_data),
            ),
            'children'             => []
        ];
    }
}
