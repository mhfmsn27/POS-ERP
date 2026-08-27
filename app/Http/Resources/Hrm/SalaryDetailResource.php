<?php

namespace App\Http\Resources\Hrm;

use Illuminate\Http\Resources\Json\JsonResource;

class SalaryDetailResource extends JsonResource
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
            'employee'      => array(
                'id'            => $this->employee->id ?? '',
                'user_id'       => $this->employee->user->id ?? '',
                'name'          => $this->employee->user->name ?? '',
                'phone'         => $this->employee->user->phone ?? '',
                'email'         => $this->employee->user->email ?? '',
                'alamat'        => $this->employee->user->address ?? '',
            ),
            'store'         => array(
                'id'            => $this->store->id ?? '',
                'name'          => $this->store->name ?? '',
                'email'         => $this->store->email ?? '',
                'phone'         => $this->store->phone ?? '',
                'address'       => $this->store->address ?? ''
            ),
            'date'          => $this->created_at,
            'designation'   => array(
                'id'            => $this->designation->id ?? '',
                'name'          => $this->designation->name ?? ''
            ),
            'commission'    => (float)$this->commission,
            'salary'        => (float)$this->salary,
            'tunjangan'     => (float)$this->allowance,
            'potongan'      => (float)$this->cutting,
            'kasbon'        => (float)$this->kasbon_total,
            'pajak'         => (float)$this->tax,
            'bonus'         => (float)$this->bonus,
            'total'         => (float)$this->total,
            'status'        => $this->status,
            'potongan_by_late'      => (float)$this->cutting_salary_by_late,
            'potongan_menit_amount' => (float)$this->cutting_munite,
            'tunjangan_list'        => SalaryItemResource::collection($this->detail->where("type", "allowance")),
            'potongan_list'         => SalaryItemResource::collection($this->detail->where("type", "deduction")),
            'info_kinerja'          =>  array(
                'absensi_bulan_ini'     => $this->attendance_this_month,
                'total_jam_kerja'       => $this->total_work,
                'total_keterlambatan'   => $this->total_late,
                'note'                  => $this->note,
            )
        ];
    }
}
