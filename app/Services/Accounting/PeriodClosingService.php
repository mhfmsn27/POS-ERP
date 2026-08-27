<?php

namespace App\Services\Accounting;

use App\Models\Account\AccountTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PeriodClosingService
{
    /**
     * Menjalankan proses Tutup Buku Bulanan atau Tahunan (*Period Closing*) dan mengunci periode.
     *
     * @param int $storeId
     * @param string $periodType 'monthly' atau 'yearly'
     * @param string $periodDate Format Y-m-d (misal: 2026-08-31)
     * @param int $userId
     * @param string|null $notes
     * @return array
     */
    public function closePeriod(
        int $storeId,
        string $periodType,
        string $periodDate,
        int $userId,
        ?string $notes = null
    ): array {
        if (!Schema::hasTable('accounting_period_closings')) {
            return ['status' => false, 'message' => 'Tabel period closing belum aktif.'];
        }

        $date = date('Y-m-d', strtotime($periodDate));
        $year = (int) date('Y', strtotime($date));
        $month = (int) date('m', strtotime($date));

        // Cek apakah sudah pernah ditutup sebelumnya
        $existing = DB::table('accounting_period_closings')
            ->where('store_id', $storeId)
            ->where('period_type', $periodType)
            ->where('period_date', $date)
            ->first();

        if ($existing && $existing->is_locked) {
            return [
                'status'  => false,
                'message' => "Periode buku {$date} ({$periodType}) sudah ditutup dan terkunci."
            ];
        }

        return DB::transaction(function () use ($storeId, $periodType, $date, $year, $month, $userId, $notes) {
            // Hitung total pendapatan (Credit nominal) dan total beban (Debit nominal)
            $query = DB::table('account_transactions')
                ->whereNull('deleted_at');

            if ($periodType === 'monthly') {
                $query->whereYear('operation_date', $year)->whereMonth('operation_date', $month);
            } else {
                $query->whereYear('operation_date', $year);
            }

            $revenue = (float) (clone $query)->where('type', 'credit')->sum('amount');
            $expense = (float) (clone $query)->where('type', 'debit')->sum('amount');
            $netProfit = $revenue - $expense;

            // Catat closing record
            DB::table('accounting_period_closings')->updateOrInsert(
                [
                    'store_id'    => $storeId,
                    'period_type' => $periodType,
                    'period_date' => $date,
                ],
                [
                    'closed_by'                => $userId,
                    'retained_earnings_amount' => $netProfit,
                    'is_locked'                => true,
                    'notes'                    => $notes ?? "Tutup buku {$periodType} per {$date}. Laba Bersih: Rp " . number_format($netProfit),
                    'created_at'               => now(),
                    'updated_at'               => now(),
                ]
            );

            return [
                'status'            => true,
                'period_date'       => $date,
                'period_type'       => $periodType,
                'total_revenue'     => $revenue,
                'total_expense'     => $expense,
                'retained_earnings' => $netProfit,
                'message'           => "Tutup buku {$periodType} ({$date}) berhasil diselesaikan. Laba Ditahan: Rp " . number_format($netProfit) . " (Periode Terkunci)."
            ];
        });
    }

    /**
     * Memeriksa apakah suatu tanggal transaksi berada dalam periode buku yang sudah dikunci (Period Lock).
     *
     * @param int $storeId
     * @param string $transactionDate
     * @return bool
     */
    public function isPeriodLocked(int $storeId, string $transactionDate): bool
    {
        if (!Schema::hasTable('accounting_period_closings')) {
            return false;
        }

        $date = date('Y-m-d', strtotime($transactionDate));

        return DB::table('accounting_period_closings')
            ->where('store_id', $storeId)
            ->where('is_locked', true)
            ->where('period_date', '>=', $date)
            ->exists();
    }
}
