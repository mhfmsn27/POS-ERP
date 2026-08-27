<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;

class CashFlowPredictorService
{
    /**
     * Memproyeksikan arus kas dan likuiditas bisnis untuk 30, 60, dan 90 hari ke depan.
     *
     * @param int|null $storeId
     * @param int $forecastDays Default 90 hari
     * @return array
     */
    public function predictCashFlow(?int $storeId = null, int $forecastDays = 90): array
    {
        // 1. Saldo Kas & Bank Saat Ini
        $cashQuery = DB::table('account_transactions')
            ->join('accounts', 'accounts.id', '=', 'account_transactions.account_id')
            ->where(function ($q) {
                $q->whereIn('accounts.sub_type', ['cash', 'bank', 'kas'])
                  ->orWhere('accounts.name', 'like', '%Kas%')
                  ->orWhere('accounts.name', 'like', '%Bank%');
            })
            ->whereNull('account_transactions.deleted_at');

        if ($storeId) {
            $cashQuery->where('accounts.store_id', $storeId);
        }

        $currentCashBalance = (float) $cashQuery->select(
            DB::raw("SUM(CASE WHEN account_transactions.type = 'debit' THEN amount ELSE -amount END) as balance")
        )->value('balance') ?? 0;

        if ($currentCashBalance < 0) {
            $currentCashBalance = 0;
        }

        // 2. Proyeksi Piutang Penjualan (Inflow yang akan tertagih)
        $receivableQuery = DB::table('transactions')
            ->where('type', 'sell')
            ->where('payment_status', 'due')
            ->whereNull('deleted_at');

        if ($storeId) {
            $receivableQuery->where('store_id', $storeId);
        }

        $totalReceivables = (float) $receivableQuery->sum('final_total');

        // 3. Proyeksi Utang Pembelian ke Supplier (Outflow yang harus dibayar)
        $payableQuery = DB::table('transactions')
            ->where('type', 'purchase')
            ->where('payment_status', 'due')
            ->whereNull('deleted_at');

        if ($storeId) {
            $payableQuery->where('store_id', $storeId);
        }

        $totalPayables = (float) $payableQuery->sum('final_total');

        // 4. Estimasi Rata-rata Beban Operasional Bulanan (dari data 60 hari terakhir)
        $sixtyDaysAgo = now()->subDays(60)->format('Y-m-d');
        $expenseQuery = DB::table('account_transactions')
            ->join('accounts', 'accounts.id', '=', 'account_transactions.account_id')
            ->where(function ($q) {
                $q->whereIn('accounts.sub_type', ['expense', 'beban', 'biaya'])
                  ->orWhere('accounts.name', 'like', '%Beban%')
                  ->orWhere('accounts.name', 'like', '%Biaya%');
            })
            ->where('account_transactions.operation_date', '>=', $sixtyDaysAgo)
            ->whereNull('account_transactions.deleted_at');

        if ($storeId) {
            $expenseQuery->where('accounts.store_id', $storeId);
        }

        $twoMonthExpenses = (float) $expenseQuery->sum('amount');
        $monthlyAvgExpense = $twoMonthExpenses > 0 ? ($twoMonthExpenses / 2) : 5000000; // Default baseline jika baru
        $dailyBurnRate = $monthlyAvgExpense / 30;

        // 5. Hitung Proyeksi Kas untuk 30, 60, dan 90 Hari
        // Asumsi penagihan piutang: 60% masuk dalam 30hr, 30% dalam 60hr, 10% dalam 90hr
        // Asumsi pembayaran utang: 50% jatuh tempo dalam 30hr, 35% dalam 60hr, 15% dalam 90hr

        $inflow30 = $totalReceivables * 0.60;
        $inflow60 = $totalReceivables * 0.90;
        $inflow90 = $totalReceivables * 1.00;

        $outflow30 = ($totalPayables * 0.50) + ($dailyBurnRate * 30);
        $outflow60 = ($totalPayables * 0.85) + ($dailyBurnRate * 60);
        $outflow90 = ($totalPayables * 1.00) + ($dailyBurnRate * 90);

        $proj30 = $currentCashBalance + $inflow30 - $outflow30;
        $proj60 = $currentCashBalance + $inflow60 - $outflow60;
        $proj90 = $currentCashBalance + $inflow90 - $outflow90;

        // 6. Hitung Runway Likuiditas Kas (Berapa hari kas bertahan tanpa penjualan baru)
        $runwayDays = $dailyBurnRate > 0 ? round($currentCashBalance / $dailyBurnRate, 1) : 999;

        if ($proj30 < 0 || $runwayDays < 15) {
            $riskStatus = 'CRITICAL_DEFICIT';
            $healthLabel = 'Risiko Defisit Tinggi (Perlu Tindakan Likuiditas Cepat)';
            $badgeColor = '#ef4444'; // Red
        } elseif ($proj60 < 0 || $runwayDays < 30) {
            $riskStatus = 'WARNING_LOW_LIQUIDITY';
            $healthLabel = 'Perlu Waspada (Penagihan Piutang Harus Dipercepat)';
            $badgeColor = '#f59e0b'; // Amber
        } else {
            $riskStatus = 'HEALTHY';
            $healthLabel = 'Likuiditas Kas Sangat Sehat';
            $badgeColor = '#10b981'; // Green
        }

        return [
            'status'               => true,
            'current_cash_balance' => $currentCashBalance,
            'total_receivables'    => $totalReceivables,
            'total_payables'       => $totalPayables,
            'monthly_burn_rate'    => round($monthlyAvgExpense, 2),
            'daily_burn_rate'      => round($dailyBurnRate, 2),
            'cash_runway_days'     => $runwayDays,
            'risk_status'          => $riskStatus,
            'health_label'         => $healthLabel,
            'badge_color'          => $badgeColor,
            'milestones'           => [
                'day_30' => [
                    'projected_inflow'  => round($inflow30, 2),
                    'projected_outflow' => round($outflow30, 2),
                    'projected_balance' => round($proj30, 2),
                ],
                'day_60' => [
                    'projected_inflow'  => round($inflow60, 2),
                    'projected_outflow' => round($outflow60, 2),
                    'projected_balance' => round($proj60, 2),
                ],
                'day_90' => [
                    'projected_inflow'  => round($inflow90, 2),
                    'projected_outflow' => round($outflow90, 2),
                    'projected_balance' => round($proj90, 2),
                ],
            ],
        ];
    }
}
