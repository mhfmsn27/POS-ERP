<?php

namespace Poshub\Ecommerce\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogDetailResource extends JsonResource
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
                  'category'        => $this->category_id,
                  'description'          => $this->description,
                  'short_description'     => $this->short_description, 
                  'thumbnail'           => asset($this->thumbnail)
            ];
      }
}
