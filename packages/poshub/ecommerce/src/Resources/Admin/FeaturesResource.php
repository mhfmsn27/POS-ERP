<?php

namespace Poshub\Ecommerce\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class FeaturesResource extends JsonResource
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
                  'id'          => $this->id,
                  'title'       => $this->title,
                  'subtitle'        => $this->subtitle,
                  'position'    => $this->position,
                  'position_name'=> $this->position_name,
                  'image'       => asset($this->image)
            ];
      }
}
