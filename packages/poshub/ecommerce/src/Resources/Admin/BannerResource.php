<?php

namespace Poshub\Ecommerce\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
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
                  'position'        => $this->position,
                  'position_name'   => $this->position_name,
                  'button'          => $this->button,
                  'button_name'     => $this->button_name,
                  'button_url'      => $this->button_url,
                  'image'           => asset($this->image)
            ];
      }
}
