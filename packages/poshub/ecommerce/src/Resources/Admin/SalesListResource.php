<?php

namespace Poshub\Ecommerce\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesListResource extends JsonResource
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
            'id'                => $this->id,
            'date'              => substr($this->transaction_date, 0, 10),
            'ref_no'            => $this->ref_no,
            'status_payment'    => $this->payment_status,
            'status'            => array(
                'pending'           => $this->status == 'hold' ? false : true,
                'process'           => $this->status == 'transit' ? false : true,
                'ordered'           => $this->status == 'ordered' ? false : true,
                'final'             => $this->status == 'final' ? true : false,
                'payment'           => $this->payment->count() > 0 ? true : false,
            ),
            'final_total'       => (float)$this->final_total,
            'due_total'         => (float)$this->due_total ?? (float)$this->final_total,
            'pay_total'         => $this->pay_total,
            'note'              => $this->additional_notes, 
            'store'             => array(
                'id'                => $this->store->id ?? '',
                'name'              => $this->store->name ?? '',
                'logo'              => asset($this->store->image_data ?? 'uploads/image-default.jpeg')
            ),
            'customer'          => array(
                'id'                => $this->customer->id ?? '',
                'name'              => $this->customer->name ?? '',
                'photo'             => asset($this->customer->photo ?? 'uploads/image-default.jpeg')
            ),
            'created'           => array(
                'id'                => $this->createdby->id ?? '',
                'name'              => $this->createdby->name ?? ''
            ),
        ];
    }
}
