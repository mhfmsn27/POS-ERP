<?php

namespace App\Services\Hrm;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PayrollComplianceService
{
    /**
     * Menentukan Kategori Tarif Efektif Rata-Rata (TER) PPh 21 berdasarkan status PTKP (PP 58/2023).
     *
     * @param string $ptkpStatus Contoh: 'TK/0', 'K/0', 'K/1', 'K/2', 'K/3'
     * @return string 'A', 'B', atau 'C'
     */
    public function getTerCategory(string $ptkpStatus): string
    {
        $status = strtoupper(trim($ptkpStatus));

        // Kategori C: K/3
        if ($status === 'K/3') {
            return 'C';
        }

        // Kategori B: TK/2, TK/3, K/1, K/2
        if (in_array($status, ['TK/2', 'TK/3', 'K/1', 'K/2'])) {
            return 'B';
        }

        // Kategori A: TK/0, TK/1, K/0 (Default)
        return 'A';
    }

    /**
     * Menghitung Tarif Efektif Bulanan PPh 21 (%) berdasarkan Kategori TER dan Penghasilan Bruto.
     *
     * @param string $category 'A', 'B', atau 'C'
     * @param float $grossSalary
     * @return float Tarif persen
     */
    public function getTerRate(string $category, float $grossSalary): float
    {
        if ($category === 'A') {
            if ($grossSalary <= 5400000) return 0.00;
            if ($grossSalary <= 5650000) return 0.25;
            if ($grossSalary <= 5950000) return 0.50;
            if ($grossSalary <= 6300000) return 0.75;
            if ($grossSalary <= 6750000) return 1.00;
            if ($grossSalary <= 7500000) return 1.25;
            if ($grossSalary <= 8550000) return 1.50;
            if ($grossSalary <= 9650000) return 1.75;
            if ($grossSalary <= 10050000) return 2.00;
            if ($grossSalary <= 10350000) return 2.25;
            if ($grossSalary <= 10700000) return 2.50;
            if ($grossSalary <= 11050000) return 3.00;
            if ($grossSalary <= 11600000) return 3.50;
            if ($grossSalary <= 12500000) return 4.00;
            if ($grossSalary <= 13750000) return 5.00;
            if ($grossSalary <= 15100000) return 6.00;
            if ($grossSalary <= 16950000) return 7.00;
            if ($grossSalary <= 19750000) return 8.00;
            if ($grossSalary <= 24150000) return 9.00;
            if ($grossSalary <= 26450000) return 10.00;
            return 15.00;
        } elseif ($category === 'B') {
            if ($grossSalary <= 6200000) return 0.00;
            if ($grossSalary <= 6500000) return 0.25;
            if ($grossSalary <= 6850000) return 0.50;
            if ($grossSalary <= 7300000) return 0.75;
            if ($grossSalary <= 9200000) return 1.00;
            if ($grossSalary <= 10750000) return 1.50;
            if ($grossSalary <= 12500000) return 2.00;
            if ($grossSalary <= 14150000) return 3.00;
            if ($grossSalary <= 16000000) return 4.00;
            if ($grossSalary <= 18000000) return 5.00;
            if ($grossSalary <= 23150000) return 7.00;
            return 12.00;
        } else { // Kategori C
            if ($grossSalary <= 6600000) return 0.00;
            if ($grossSalary <= 6950000) return 0.25;
            if ($grossSalary <= 7350000) return 0.50;
            if ($grossSalary <= 7800000) return 0.75;
            if ($grossSalary <= 8850000) return 1.00;
            if ($grossSalary <= 11200000) return 1.50;
            if ($grossSalary <= 15150000) return 3.00;
            if ($grossSalary <= 19750000) return 5.00;
            return 10.00;
        }
    }

    /**
     * Menghitung rincian Take-Home-Pay, Pajak PPh 21 TER, dan BPJS Ketenagakerjaan/Kesehatan.
     *
     * @param int $employeeId
     * @param float $grossSalary
     * @param string $ptkpStatus
     * @return array
     */
    public function calculatePayroll(int $employeeId, float $grossSalary, string $ptkpStatus = 'TK/0'): array
    {
        $employee = \App\Models\Hrm\Employee::withoutGlobalScopes()->with('user')->find($employeeId);
        $empName  = $employee->user->name ?? $employee->name ?? "Karyawan #{$employeeId}";

        $category = $this->getTerCategory($ptkpStatus);
        $ratePercent = $this->getTerRate($category, $grossSalary);
        $pph21 = round(($ratePercent / 100) * $grossSalary, 2);

        // BPJS Ketenagakerjaan:
        // JHT Pekerja 2%, Perusahaan 3.7%
        $bpjsTkEmployee = round($grossSalary * 0.02, 2);
        $bpjsTkCompany  = round($grossSalary * 0.037, 2);

        // BPJS Kesehatan:
        // Pekerja 1%, Perusahaan 4% (Batas Maksimal Gaji BPJS Kes Rp 12.000.000)
        $cappedKes = min($grossSalary, 12000000);
        $bpjsKesEmployee = round($cappedKes * 0.01, 2);
        $bpjsKesCompany  = round($cappedKes * 0.04, 2);

        $totalDeductions = $pph21 + $bpjsTkEmployee + $bpjsKesEmployee;
        $takeHomePay = $grossSalary - $totalDeductions;

        return [
            'employee_id'       => $employeeId,
            'employee_name'     => $empName,
            'gross_salary'      => $grossSalary,
            'ptkp_status'       => $ptkpStatus,
            'ter_category'      => $category,
            'ter_rate_percent'  => $ratePercent,
            'pph21_amount'      => $pph21,
            'bpjs_tk_employee'  => $bpjsTkEmployee,
            'bpjs_tk_company'   => $bpjsTkCompany,
            'bpjs_kes_employee' => $bpjsKesEmployee,
            'bpjs_kes_company'  => $bpjsKesCompany,
            'total_deductions'  => $totalDeductions,
            'net_take_home_pay' => $takeHomePay,
        ];
    }
}
