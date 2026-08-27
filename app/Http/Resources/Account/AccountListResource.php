<?php

namespace App\Http\Resources\Account;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'coa'       => $this->coa,
            'autocode'  => false,
            'balance'   => number_format($this->cashflow_data),
            'subtype'   => $this->is_root_parent == 'yes' ? true : false,
            'status'    => $this->closed == 'no' ? false : true,
            'type_account'  => $this->type_account,
            'store'     => array(
                'id'        => $this->store->id ?? '',
                'name'      => $this->store->name ?? ''
            ),
            'type'      => array(
                'id'        => $this->type->id ?? '',
                'name'      => $this->type->name ?? '',
                'type'      => $this->type->type ?? ''
            ),
            'user'      => array(
                'id'        => $this->user->id ?? '',
                'name'      => $this->user->name ?? ''
            ),
            'account'   => array(
                'id'        => $this->parent->id ?? '',
                'name'      => $this->parent->name ?? ''
            ),
            'note'      => $this->note,
            'children'      => AccountListResource::collection($this->child)
        ];
    }
}
