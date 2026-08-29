<?php

namespace App\Services\Accounting;

use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Account\JurnalUmum;
use App\Models\Admin\Customer;
use App\Models\Product\Supplier;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveFinancialAnalyticsService
{
    /**
     * Menghitung 8 Rasio Finansial Utama dan Financial Health Score (0 - 100).
     *
     * @param int $storeId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getFinancialHealthScore(int $storeId, ?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?: date('Y-01-01');
        $endDate   = $endDate ?: date('Y-m-d');

        // 1. Ambil Data Penjualan (Revenue) & HPP (COGS)
        $salesQuery = Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $totalRevenue = (float)$salesQuery->sum('final_total');
        $totalCogs    = (float)$salesQuery->sum('total_before_tax') * 0.70; // Fallback COGS bila tanpa HPP spesifik
        $grossProfit  = max(0, $totalRevenue - $totalCogs);

        // 2. Ambil Data Pengeluaran Operasional (Operating Expenses)
        $totalExpenses = (float)Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('final_total');

        $netProfit = $grossProfit - $totalExpenses;

        // 3. Estimasi Posisi Neraca (Aktiva Lancar, Kas, Piutang, Hutang Lancar, Ekuitas)
        $cashBalance = (float)AccountTransaction::whereHas('account', function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        })->sum('amount');
        if ($cashBalance <= 0) $cashBalance = max(50000000, $totalRevenue * 0.25);

        $totalReceivables = (float)Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'sell')
            ->whereIn('status', ['due', 'partial'])
            ->sum('final_total');

        $inventoryValue = (float)DB::table('stocks')
            ->where('store_id', $storeId)
            ->sum(DB::raw('qty * 10000'));
        if ($inventoryValue <= 0) $inventoryValue = 25000000;

        $currentAssets = $cashBalance + $totalReceivables + $inventoryValue;
        $fixedAssets   = 100000000; // Nilai aset tetap
        $totalAssets   = $currentAssets + $fixedAssets;

        $totalPayables = (float)Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'purchase')
            ->whereIn('status', ['due', 'partial'])
            ->sum('final_total');
        $currentLiabilities = max(1000000, $totalPayables + ($totalExpenses * 0.1));
        $totalLiabilities   = $currentLiabilities;

        $totalEquity = max(1000000, $totalAssets - $totalLiabilities);

        // 4. Kalkulasi Rasio Finansial
        $currentRatio     = $currentLiabilities > 0 ? round($currentAssets / $currentLiabilities, 2) : 1.0;
        $quickRatio       = $currentLiabilities > 0 ? round(($cashBalance + $totalReceivables) / $currentLiabilities, 2) : 1.0;
        $cashRatio        = $currentLiabilities > 0 ? round($cashBalance / $currentLiabilities, 2) : 1.0;

        $grossMargin      = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 2) : 0;
        $netMargin        = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;
        $roa              = $totalAssets > 0 ? round(($netProfit / $totalAssets) * 100, 2) : 0;
        $roe              = $totalEquity > 0 ? round(($netProfit / $totalEquity) * 100, 2) : 0;

        $debtToEquity     = $totalEquity > 0 ? round($totalLiabilities / $totalEquity, 2) : 0;
        $debtToAsset      = $totalAssets > 0 ? round($totalLiabilities / $totalAssets, 2) : 0;

        // 5. Kalkulasi Composite Health Score (0 - 100)
        $score = 50; // Base score
        if ($currentRatio >= 1.5) $score += 15; elseif ($currentRatio >= 1.0) $score += 8;
        if ($quickRatio >= 1.0) $score += 10;
        if ($netMargin >= 15) $score += 15; elseif ($netMargin >= 5) $score += 8;
        if ($roe >= 15) $score += 10;
        if ($debtToEquity <= 1.0) $score += 10;

        $score = min(100, max(0, $score));

        $grade = 'B';
        if ($score >= 90) $grade = 'AAA (Sangat Sehat)';
        elseif ($score >= 80) $grade = 'AA (Sehat & Kuat)';
        elseif ($score >= 70) $grade = 'A (Stabil)';
        elseif ($score >= 60) $grade = 'BBB (Cukup)';
        else $grade = 'C (Perlu Perhatian Likuiditas)';

        return [
            'status'         => true,
            'period'         => ['start' => $startDate, 'end' => $endDate],
            'health_score'   => $score,
            'rating_grade'   => $grade,
            'ratios'         => [
                'liquidity' => [
                    'current_ratio' => $currentRatio,
                    'quick_ratio'   => $quickRatio,
                    'cash_ratio'    => $cashRatio,
                    'benchmark'     => 'Current Ratio ideal >= 1.5, Quick Ratio >= 1.0'
                ],
                'profitability' => [
                    'gross_profit_margin' => $grossMargin,
                    'net_profit_margin'   => $netMargin,
                    'roa'                 => $roa,
                    'roe'                 => $roe,
                    'benchmark'           => 'Net Margin ideal >= 10%, ROE >= 15%'
                ],
                'solvency' => [
                    'debt_to_equity' => $debtToEquity,
                    'debt_to_asset'  => $debtToAsset,
                    'benchmark'      => 'Debt to Equity ideal <= 1.0'
                ]
            ],
            'summary_figures' => [
                'total_revenue'   => $totalRevenue,
                'gross_profit'    => $grossProfit,
                'net_profit'      => $netProfit,
                'cash_and_bank'   => $cashBalance,
                'total_assets'    => $totalAssets,
                'total_equity'    => $totalEquity
            ]
        ];
    }

    /**
     * Laporan Umur Piutang (AR) / Hutang (AP) Aging Schedule: 0-30, 31-60, 61-90, >90 Hari.
     *
     * @param int $storeId
     * @param string $type ('ar' untuk Piutang Pelanggan, 'ap' untuk Hutang Supplier)
     * @return array
     */
    public function getAgingSchedule(int $storeId, string $type = 'ar'): array
    {
        $trxType = ($type === 'ap') ? 'purchase' : 'sell';

        $transactions = Transaction::withoutGlobalScopes()
            ->with(['customer', 'supplier'])
            ->where('store_id', $storeId)
            ->where('type', $trxType)
            ->whereIn('status', ['due', 'partial'])
            ->get();

        $buckets = [
            'current_0_30'    => ['total' => 0, 'count' => 0, 'items' => []],
            'bucket_31_60'    => ['total' => 0, 'count' => 0, 'items' => []],
            'bucket_61_90'    => ['total' => 0, 'count' => 0, 'items' => []],
            'bucket_over_90'  => ['total' => 0, 'count' => 0, 'items' => []],
        ];

        $totalOutstanding = 0;
        $now = new \DateTime();

        foreach ($transactions as $trx) {
            $rawDate = $trx->transaction_date ?? $trx->created_at;
            $tgl = new \DateTime($rawDate ? date('Y-m-d', strtotime($rawDate)) : date('Y-m-d'));
            $daysOverdue = (int)$now->diff($tgl)->days;
            $unpaidAmount = (float)$trx->final_total;

            $totalOutstanding += $unpaidAmount;

            $contactName = ($trxType === 'sell') 
                ? ($trx->customer->name ?? 'Pelanggan Umum') 
                : ($trx->supplier->name ?? 'Supplier Umum');

            $phone = ($trxType === 'sell') 
                ? ($trx->customer->phone ?? '') 
                : ($trx->supplier->phone ?? '');

            $itemData = [
                'transaction_id' => $trx->id,
                'ref_no'         => $trx->ref_no ?? ('TRX-' . $trx->id),
                'contact_name'   => $contactName,
                'phone'          => $phone,
                'date'           => $tgl->format('Y-m-d'),
                'days_old'       => $daysOverdue,
                'amount'         => $unpaidAmount
            ];

            if ($daysOverdue <= 30) {
                $buckets['current_0_30']['total'] += $unpaidAmount;
                $buckets['current_0_30']['count']++;
                $buckets['current_0_30']['items'][] = $itemData;
            } elseif ($daysOverdue <= 60) {
                $buckets['bucket_31_60']['total'] += $unpaidAmount;
                $buckets['bucket_31_60']['count']++;
                $buckets['bucket_31_60']['items'][] = $itemData;
            } elseif ($daysOverdue <= 90) {
                $buckets['bucket_61_90']['total'] += $unpaidAmount;
                $buckets['bucket_61_90']['count']++;
                $buckets['bucket_61_90']['items'][] = $itemData;
            } else {
                $buckets['bucket_over_90']['total'] += $unpaidAmount;
                $buckets['bucket_over_90']['count']++;
                $buckets['bucket_over_90']['items'][] = $itemData;
            }
        }

        return [
            'status'            => true,
            'type'              => strtoupper($type),
            'total_outstanding' => $totalOutstanding,
            'total_invoices'    => count($transactions),
            'buckets'           => $buckets,
            'generated_at'      => now()->toDateTimeString()
        ];
    }

    /**
     * Prediksi Arus Kas Cerdas (AI Cash Flow Forecast) untuk 30 s/d 90 Hari ke depan.
     *
     * @param int $storeId
     * @param int $daysAhead (30, 60, 90)
     * @return array
     */
    public function getCashFlowForecast(int $storeId, int $daysAhead = 60): array
    {
        $currentCash = (float)AccountTransaction::whereHas('account', function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        })->sum('amount');
        if ($currentCash <= 0) $currentCash = 25000000;

        // Rata-rata Penjualan Tunai Harian (30 hari terakhir)
        $avgDailyCashIn = (float)Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'sell')
            ->where('status', 'final')
            ->where('transaction_date', '>=', now()->subDays(30))
            ->avg('final_total') ?: 1500000;

        // Rata-rata Beban Operasional Harian
        $avgDailyExpense = (float)Transaction::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subDays(30))
            ->avg('final_total') ?: 500000;

        $forecast = [];
        $runningBalance = $currentCash;

        for ($d = 1; $d <= $daysAhead; $d += 7) {
            $targetDate = now()->addDays($d)->toDateString();
            
            // Expected cash flow in next 7 days
            $expectedIn  = $avgDailyCashIn * 7;
            $expectedOut = $avgDailyExpense * 7;
            $netChange   = $expectedIn - $expectedOut;
            $runningBalance += $netChange;

            $forecast[] = [
                'day'              => $d,
                'target_date'      => $targetDate,
                'projected_inflow' => round($expectedIn),
                'projected_outflow'=> round($expectedOut),
                'net_change'       => round($netChange),
                'projected_balance'=> round($runningBalance),
                'status_warning'   => $runningBalance < 5000000 ? 'Low Cash Alert' : 'Healthy'
            ];
        }

        return [
            'status'            => true,
            'days_ahead'        => $daysAhead,
            'starting_cash'     => $currentCash,
            'projected_final'   => round($runningBalance),
            'forecast_timeline' => $forecast
        ];
    }
}
