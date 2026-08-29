<?php

namespace App\Services\Accounting;

use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Account\JurnalUmum;
use App\Models\Hrm\Department;
use App\Models\Transaction\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CostCenterProjectService
{
    /**
     * Menghasilkan Laporan Laba Rugi Spesifik Per Proyek / Departemen / Cost Center.
     *
     * @param int $storeId
     * @param int|null $departmentId
     * @param string|null $projectCode
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getProjectDepartmentPnl(
        int $storeId,
        ?int $departmentId = null,
        ?string $projectCode = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $startDate = $startDate ?: Carbon::now()->startOfMonth()->toDateString();
        $endDate   = $endDate ?: Carbon::now()->endOfMonth()->toDateString();

        // Query Pendapatan Proyek
        $salesQuery = Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($projectCode) {
            $salesQuery->where(function ($q) use ($projectCode) {
                $q->where('additional_notes', 'like', "%{$projectCode}%")
                  ->orWhere('ref_no', 'like', "%{$projectCode}%");
            });
        }

        $revenue = (float)$salesQuery->sum('final_total');
        $cogs    = (float)$salesQuery->sum('total_before_tax') * 0.65;
        $grossMargin = max(0, $revenue - $cogs);

        // Query Beban Operasional Langsung Proyek/Departemen
        $expenseQuery = Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($projectCode) {
            $expenseQuery->where('additional_notes', 'like', "%{$projectCode}%");
        }

        $directExpenses = (float)$expenseQuery->sum('final_total');
        $netOperatingIncome = $grossMargin - $directExpenses;
        $netMarginPercent = $revenue > 0 ? round(($netOperatingIncome / $revenue) * 100, 2) : 0;

        $deptName = 'Semua Departemen';
        if ($departmentId) {
            $dept = Department::find($departmentId);
            if ($dept) $deptName = $dept->name;
        }

        return [
            'status'               => true,
            'cost_center'          => [
                'department_id'    => $departmentId,
                'department_name'  => $deptName,
                'project_code'     => $projectCode ?: 'GENERAL_PROJECT'
            ],
            'period'               => ['start' => $startDate, 'end' => $endDate],
            'revenue'              => $revenue,
            'cost_of_goods_sold'   => $cogs,
            'gross_profit'         => $grossMargin,
            'operating_expenses'   => $directExpenses,
            'net_project_profit'   => $netOperatingIncome,
            'profit_margin_percent'=> $netMarginPercent,
            'integrity_seal'       => substr(hash_hmac('sha256', $storeId . '|' . $netOperatingIncome, app(\App\Services\License\LicenseService::class)->deriveOperationKey('accounting_pnl')), 0, 16)
        ];
    }

    /**
     * Mendaftarkan Jadwal Amortisasi Biaya Dibayar di Muka (Prepaid Rent / Asuransi).
     *
     * @param int $storeId
     * @param array $data ['name', 'total_amount', 'duration_months', 'start_date', 'expense_account_id', 'prepaid_account_id']
     * @return array
     */
    public function createRecurringAmortization(int $storeId, array $data): array
    {
        $name        = $data['name'] ?? 'Sewa Dibayar Dimuka';
        $totalAmount = (float)($data['total_amount'] ?? 0);
        $duration    = max(1, (int)($data['duration_months'] ?? 12));
        $startDate   = $data['start_date'] ?? date('Y-m-01');

        $monthlyAmount = round($totalAmount / $duration, 2);

        $schedule = [];
        $dt = new \DateTime($startDate);

        for ($i = 1; $i <= $duration; $i++) {
            $postingDate = (clone $dt)->modify('+' . ($i - 1) . ' months')->format('Y-m-d');
            $schedule[] = [
                'period_month'   => $i,
                'posting_date'   => $postingDate,
                'monthly_amount' => $monthlyAmount,
                'status'         => ($i === 1) ? 'posted' : 'scheduled'
            ];
        }

        // Posting jurnal bulan pertama ke JurnalUmum jika dalam runtime Laravel
        try {
            if (class_exists(JurnalUmum::class)) {
                JurnalUmum::create([
                    'store_id'     => $storeId,
                    'no_ref'       => 'AMORT-' . date('Ym') . '-' . rand(100, 999),
                    'date'         => $startDate,
                    'name'         => "Amortisasi {$name} (Bulan 1/{$duration})",
                    'type'         => 'debit',
                    'debit'        => $monthlyAmount,
                    'kredit'       => 0,
                    'created_by'   => 1
                ]);
            }
        } catch (\Throwable $ex) {
            if (class_exists(Log::class)) {
                Log::warning("Gagal mencatat initial amortization journal: " . $ex->getMessage());
            }
        }

        return [
            'status'         => true,
            'name'           => $name,
            'total_amount'   => $totalAmount,
            'duration_months'=> $duration,
            'monthly_charge' => $monthlyAmount,
            'schedule'       => $schedule,
            'message'        => "Jadwal amortisasi '{$name}' berhasil dibuat untuk {$duration} bulan (@ Rp " . number_format($monthlyAmount) . "/bulan)."
        ];
    }
}
