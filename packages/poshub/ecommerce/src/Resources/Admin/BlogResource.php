<?php

namespace Poshub\Ecommerce\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
                  'id'              => $this->id,
                  'title'           => $this->title,
                  'category'        => $this->category->name ?? '',
                  'thumbnail'       => asset($this->thumbnail)
            ];
      }
}
