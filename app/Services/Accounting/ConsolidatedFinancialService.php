<?php

namespace App\Services\Accounting;

use App\Models\Admin\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ConsolidatedFinancialService
{
    /**
     * Menghasilkan Laporan Laba Rugi Konsolidasi Multi-Cabang dengan Eliminasi Transaksi Antar Cabang.
     *
     * @param array|null $storeIds
     * @param string $startDate Y-m-d
     * @param string $endDate Y-m-d
     * @return array
     */
    public function getConsolidatedIncomeStatement(?array $storeIds = null, string $startDate = '', string $endDate = ''): array
    {
        if (empty($startDate)) {
            $startDate = now()->startOfMonth()->format('Y-m-d');
        }
        if (empty($endDate)) {
            $endDate = now()->endOfMonth()->format('Y-m-d');
        }

        // Ambil daftar toko/cabang yang akan dikonsolidasikan
        $storeQuery = Store::withoutGlobalScopes()->where('status', 'active');
        if (!empty($storeIds)) {
            $storeQuery->whereIn('id', $storeIds);
        }
        $stores = $storeQuery->get(['id', 'name']);

        $storeStatements = [];
        $totalGrossRevenue = 0;
        $totalCOGS = 0;
        $totalOperatingExpense = 0;
        $totalNetProfit = 0;

        foreach ($stores as $store) {
            $sId = $store->id;

            // 1. Total Pendapatan Toko
            $revenue = (float) DB::table('account_transactions')
                ->join('accounts', 'accounts.id', '=', 'account_transactions.account_id')
                ->leftJoin('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->whereBetween('account_transactions.operation_date', [$startDate, $endDate])
                ->whereIn('accounts.sub_type', ['revenue', 'pendapatan', 'sales'])
                ->whereNull('account_transactions.deleted_at')
                ->where(function ($q) use ($sId) {
                    $q->where('accounts.store_id', $sId)
                      ->orWhere('transactions.store_id', $sId);
                })
                ->select(DB::raw("SUM(CASE WHEN account_transactions.type = 'credit' THEN amount ELSE -amount END) as total"))
                ->value('total') ?? 0;

            // 2. HPP (COGS)
            $cogs = (float) DB::table('account_transactions')
                ->join('accounts', 'accounts.id', '=', 'account_transactions.account_id')
                ->leftJoin('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->whereBetween('account_transactions.operation_date', [$startDate, $endDate])
                ->whereIn('accounts.sub_type', ['cogs', 'hpp', 'product_cost'])
                ->whereNull('account_transactions.deleted_at')
                ->where(function ($q) use ($sId) {
                    $q->where('accounts.store_id', $sId)
                      ->orWhere('transactions.store_id', $sId);
                })
                ->select(DB::raw("SUM(CASE WHEN account_transactions.type = 'debit' THEN amount ELSE -amount END) as total"))
                ->value('total') ?? 0;

            // 3. Beban Operasional
            $opex = (float) DB::table('account_transactions')
                ->join('accounts', 'accounts.id', '=', 'account_transactions.account_id')
                ->leftJoin('transactions', 'transactions.id', '=', 'account_transactions.transaction_id')
                ->whereBetween('account_transactions.operation_date', [$startDate, $endDate])
                ->whereIn('accounts.sub_type', ['expense', 'beban', 'biaya'])
                ->whereNull('account_transactions.deleted_at')
                ->where(function ($q) use ($sId) {
                    $q->where('accounts.store_id', $sId)
                      ->orWhere('transactions.store_id', $sId);
                })
                ->select(DB::raw("SUM(CASE WHEN account_transactions.type = 'debit' THEN amount ELSE -amount END) as total"))
                ->value('total') ?? 0;

            $grossProfit = $revenue - $cogs;
            $netProfit = $grossProfit - $opex;

            $storeStatements[] = [
                'store_id'          => $sId,
                'store_name'        => $store->name,
                'revenue'           => $revenue,
                'cogs'              => $cogs,
                'gross_profit'      => $grossProfit,
                'operating_expense' => $opex,
                'net_profit'        => $netProfit,
            ];

            $totalGrossRevenue += $revenue;
            $totalCOGS += $cogs;
            $totalOperatingExpense += $opex;
            $totalNetProfit += $netProfit;
        }

        // 4. Hitung Eliminasi Transaksi Antar Cabang (Intercompany Elimination)
        $intercompanyElimination = 0;
        if (Schema::hasTable('store_transfers')) {
            $intercompanyQuery = DB::table('store_transfers')
                ->where('status', 'received')
                ->whereBetween('created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);

            if (!empty($storeIds)) {
                $intercompanyQuery->whereIn('from_store_id', $storeIds)->whereIn('to_store_id', $storeIds);
            }

            // Estimasi nominal eliminasi mutasi internal
            $intercompanyElimination = (float) $intercompanyQuery->sum('total_qty_received') * 10000; // Standar base cost unit
        }

        $consolidatedRevenue = max(0, $totalGrossRevenue - $intercompanyElimination);
        $consolidatedCOGS = max(0, $totalCOGS - $intercompanyElimination);
        $consolidatedGrossProfit = $consolidatedRevenue - $consolidatedCOGS;
        $consolidatedNetProfit = $consolidatedGrossProfit - $totalOperatingExpense;

        return [
            'period'                    => ['start_date' => $startDate, 'end_date' => $endDate],
            'branch_breakdowns'         => $storeStatements,
            'unadjusted_revenue'        => $totalGrossRevenue,
            'intercompany_elimination'  => $intercompanyElimination,
            'consolidated_revenue'      => $consolidatedRevenue,
            'consolidated_cogs'         => $consolidatedCOGS,
            'consolidated_gross_profit' => $consolidatedGrossProfit,
            'consolidated_operating_exp'=> $totalOperatingExpense,
            'consolidated_net_profit'   => $consolidatedNetProfit,
        ];
    }
}
