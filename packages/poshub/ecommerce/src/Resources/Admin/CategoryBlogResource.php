<?php

namespace Poshub\Ecommerce\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryBlogResource extends JsonResource
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
                  'name'            => $this->name,
                  'image'           => asset($this->image),
                  'blogs'           => (int)$this->blog->count() 
            ];
      }
}
