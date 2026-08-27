<?php

namespace App\Services\Accounting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BudgetingService
{
    /**
     * Menetapkan pagu anggaran departemen/cabang per akun beban.
     *
     * @param int $storeId
     * @param int|null $departmentId
     * @param int $year
     * @param int|null $month
     * @param int $accountId
     * @param float $amount
     * @return array
     */
    public function setBudget(int $storeId, ?int $departmentId, int $year, ?int $month, int $accountId, float $amount): array
    {
        if (!Schema::hasTable('department_budgets')) {
            return ['status' => false, 'message' => 'Tabel department_budgets belum tersedia.'];
        }

        $existing = DB::table('department_budgets')
            ->where('store_id', $storeId)
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->where('account_id', $accountId)
            ->first();

        if ($existing) {
            DB::table('department_budgets')->where('id', $existing->id)->update([
                'budget_amount' => $amount,
                'department_id' => $departmentId,
                'updated_at'    => now(),
            ]);
            $id = $existing->id;
        } else {
            $id = DB::table('department_budgets')->insertGetId([
                'store_id'      => $storeId,
                'department_id' => $departmentId,
                'period_year'   => $year,
                'period_month'  => $month,
                'account_id'    => $accountId,
                'budget_amount' => $amount,
                'actual_spent'  => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        return [
            'status'    => true,
            'budget_id' => $id,
            'message'   => 'Pagu anggaran berhasil disimpan.'
        ];
    }

    /**
     * Mengambil laporan analisa varians anggaran vs pengeluaran aktual (Budget vs Actual Variance).
     *
     * @param int $storeId
     * @param int $year
     * @param int|null $month
     * @return array
     */
    public function getBudgetVarianceReport(int $storeId, int $year, ?int $month = null): array
    {
        if (!Schema::hasTable('department_budgets')) {
            return ['status' => true, 'report' => []];
        }

        $query = DB::table('department_budgets')
            ->join('accounts', 'accounts.id', '=', 'department_budgets.account_id')
            ->where('department_budgets.store_id', $storeId)
            ->where('department_budgets.period_year', $year);

        if ($month) {
            $query->where('department_budgets.period_month', $month);
        }

        $budgets = $query->select(
            'department_budgets.*',
            'accounts.name as account_name',
            'accounts.code as account_code'
        )->get();

        $rows = [];
        $totalBudget = 0;
        $totalActual = 0;

        foreach ($budgets as $b) {
            // Hitung pengeluaran aktual langsung dari buku besar account_transactions
            $spentQuery = DB::table('account_transactions')
                ->where('account_id', $b->account_id)
                ->whereYear('operation_date', $year)
                ->whereNull('deleted_at');

            if ($month) {
                $spentQuery->whereMonth('operation_date', $month);
            }

            $actual = (float) $spentQuery->select(
                DB::raw("SUM(CASE WHEN type = 'credit' THEN -amount ELSE amount END) as net_spent")
            )->value('net_spent') ?? 0;

            if ($actual < 0) {
                $actual = 0;
            }

            $budget = (float) $b->budget_amount;
            $variance = $budget - $actual;
            $utilizationPercent = $budget > 0 ? round(($actual / $budget) * 100, 1) : 0;

            if ($utilizationPercent >= 100) {
                $status = 'OVER_BUDGET';
                $badgeColor = '#ef4444';
            } elseif ($utilizationPercent >= 80) {
                $status = 'WARNING_NEAR_LIMIT';
                $badgeColor = '#f59e0b';
            } else {
                $status = 'ON_TRACK';
                $badgeColor = '#10b981';
            }

            $rows[] = [
                'id'                  => $b->id,
                'account_name'        => $b->account_name,
                'account_code'        => $b->account_code,
                'budget_amount'       => $budget,
                'actual_spent'        => $actual,
                'variance_amount'     => $variance,
                'utilization_percent' => $utilizationPercent,
                'status'              => $status,
                'badge_color'         => $badgeColor,
            ];

            $totalBudget += $budget;
            $totalActual += $actual;
        }

        return [
            'status'         => true,
            'year'           => $year,
            'month'          => $month,
            'total_budget'   => $totalBudget,
            'total_actual'   => $totalActual,
            'total_variance' => $totalBudget - $totalActual,
            'overall_usage'  => $totalBudget > 0 ? round(($totalActual / $totalBudget) * 100, 1) : 0,
            'report'         => $rows,
        ];
    }
}
