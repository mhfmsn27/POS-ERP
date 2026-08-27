<?php

namespace Poshub\Ecommerce\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
{
      
      /**
       * Transform the resource into an array.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
       */
      public function toArray($request)
      {

            $status = '';

            if ($this->payment_status == 'due') {
                  $status = 'Menunggu Pembayaran';
            } else {
                  if ($this->status == 'ordered') {
                        $status = 'Sedang Di Kemas';
                  }

                  if ($this->status == 'transit') {
                        $status = 'Dalam Perjalanan';
                  }

                  if ($this->status == 'complete') {
                        $status = "Selesai";
                  }
            }

            return [
                  'id'                => $this->id,
                  'ref_no'            => $this->ref_no,
                  'transaction_date'  => $this->created_at->format("Y-m-d"),
                  'status_text'       => $status,
                  'payment_status'    => $this->payment_status,
                  'status'            => $this->status,
                  'subtotal'          => (int)$this->total_before_tax,
                  'tax_total'         => (int)$this->tax_amount,
                  'shipping_cost'     => (int)$this->shipping_charges,
                  'grand_total'       => (int)$this->final_total,
                  'store'             => array(
                        'name'            => $this->store->name ?? '',
                        'address'         => $this->store->address ?? '',
                        'phone'           => $this->store->phone ?? '',
                  ),
                  'customer'          => array(
                        'name'            => $this->customer->name ?? '',
                        'email'           => $this->customer->email ?? '',
                        'phone'           => $this->customer->phone ?? '',
                        'address'         => $this->customer->address ?? ''
                  ),
                  'pengiriman'        => array( 
                        'curir_name'      => $this->shipping_detail->curir_name ?? '',
                        'curir_service'   => $this->shipping_detail->curir_service ?? '', 
                        'postal_code'     => $this->shipping_detail->postal_code ?? '',
                        'address_detail'  => $this->shipping_detail->address_detail ?? '',
                        'phone'           => $this->shipping_detail->phone ?? '', 
                        'province'        => $this->shipping_detail->subdistrict->city->province->name ?? '',
                        'city'            => $this->shipping_detail->subdistrict->city->name ?? '',
                        'district'        => $this->shipping_detail->subdistrict->name ?? '',
                        'name'            => $this->shipping_detail->name ?? '',
                        'resi_no'         => $this->shipping_detail->resi_no ?? '',
                  ),
                  'items'             => SellItemResource::collection($this->sell)
            ];
      }
}
