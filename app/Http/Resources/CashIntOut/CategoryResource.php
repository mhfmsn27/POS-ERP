<?php

namespace App\Http\Resources\CashIntOut;

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
            'id'        => $this->id,
            'name'      => $this->name,
            'detail'    => $this->detail,
            'cash_out'  => $this->expense->where("type", "expense")->count(),
            'cash_int'  => $this->expense->where("type", "cash_int")->count()
        ];
    }
}
