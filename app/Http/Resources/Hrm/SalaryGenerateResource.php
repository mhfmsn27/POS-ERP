<?php

namespace App\Http\Resources\Hrm;

use App\Models\Admin\SettingsHrm;
use App\Models\Admin\Store;
use App\Models\Salary\Allowance;
use App\Models\Salary\CuttingSalary;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryGenerateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $year           = substr($request->date, 0, 4);
        $month          = substr($request->date, 5, 2);
        $store          = Store::find(my_store());
        $calendar       = CAL_GREGORIAN;
        $day            = cal_days_in_month($calendar, $month, $year);
        $setthrm        = SettingsHrm::first(['salary_tax', 'attendance_to_cutting', 'attendance_to_salary', 'salary_tax']);
        $listTunjangan  = [];
        $listPotongan   = [];
        $commission     = $this->user->total_commission ?? 0;

        $allowance      = Allowance::all();
        $cutting        = CuttingSalary::all();

        $kasbonTotal    = $this->due_total < 0 ? abs($this->due_total) : 0;
        $totalTunjangan = 0;
        $totalPotongan  = 0;

        foreach ($allowance as $a) {

            $day                = $a->priode == 'month' ? 1 : ($setthrm->attendance_to_salary == 'no' ? cal_days_in_month($calendar, $month, $year) : $this->month_total($year, $month));
            $total_allowance    = $a->amount * $day;
            $totalTunjangan     += $total_allowance;

            $listTunjangan[] = array(
                'name'              => $a->name,
                'jumlah'            => (int)$a->amount,
                'hari'              => $day,
                'total'             => $total_allowance,
            );
        }

        foreach ($cutting as $c) {

            $day                = $c->priode == 'month' ? 1 : ($setthrm->attendance_to_cutting == 'no' ? cal_days_in_month($calendar, $month, $year) : $this->month_total($year, $month));
            $total_potongan     = $c->amount * $day;
            $totalPotongan     += $total_potongan;

            $listPotongan[] = array(
                'name'              => $c->name,
                'jumlah'            => (int)$c->amount,
                'hari'              => $day,
                'total'             => $total_potongan,
            );
        }


        $subtotal       = $this->salary - $kasbonTotal - $totalPotongan + $totalTunjangan + $commission;
        $tax            = $setthrm->salary_tax > 0 ? ($setthrm->salary_tax / 100) * $this->salary : 0;
        $total          = $subtotal - $tax;

        return [ 
            'commission'                => $commission,
            'tunjangan'                 => $listTunjangan,
            'potongan'                  => $listPotongan,
            'kasbon'                    => $kasbonTotal,
            'total_tunjangan'           => $totalTunjangan,
            'total_potongan'            => $totalPotongan,
            'salary'                    => (int)$this->salary,
            'subtotal'                  => (int)$subtotal,
            'pajak'                     => (int)$setthrm->salary_tax,
            'bonus'                     => 0,
            'total'                     => (int)$total,
            'after_bonus'               => (int)$total,
            'catatan'                   => '',
            'store_information'         => array(
                'id'                        => $store->id,
                'name'                      => $store->name,
                'email'                     => $store->email,
                'phone'                     => $store->phone,
                'address'                   => $store->address
            ),
            'employee_information'      => array(
                'id'                        => $this->id,
                'user_id'                   => $this->user->id ?? '',
                'name'                      => $this->user->name ?? '',
                'phone'                     => $this->user->phone ?? '',
                'email'                     => $this->user->email ?? '',
                'alamat'                    => $this->user->address ?? '',
                'designation'               => array(
                    'id'                        => $this->designation->id ?? '',
                    'name'                      => $this->designation->name ?? ''
                ),
                'absensi_bulanan'           => $this->month_total($year, $month),
                'total_jam_kerja'           => $this->month_work($year, $month),
                'total_keterlambatan'       => $this->month_late($year, $month)
            )
        ];
    }
}
