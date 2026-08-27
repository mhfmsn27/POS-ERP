<?php

namespace App\Services\Accounting;

use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class FiscalPeriodService
{
    /**
     * Memeriksa apakah tanggal transaksi berada di periode yang terkunci / tertutup.
     *
     * @param string $date Y-m-d
     * @param int|null $storeId
     * @return bool
     */
    public function isPeriodLocked(string $date, ?int $storeId = null): bool
    {
        if (empty($date)) {
            return false;
        }

        try {
            if (!Schema::hasTable('fiscal_periods')) {
                return false;
            }

            $query = DB::table('fiscal_periods')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->whereIn('status', ['locked', 'closed']);

            if ($storeId !== null) {
                $query->where(function ($q) use ($storeId) {
                    $q->where('store_id', $storeId)->orWhereNull('store_id');
                });
            }

            return $query->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Mengunci atau membuka periode akuntansi.
     *
     * @param int $periodId
     * @param string $status 'open' | 'locked' | 'closed'
     * @param int|null $userId
     * @param string|null $notes
     * @return array
     */
    public function updatePeriodStatus(int $periodId, string $status, ?int $userId = null, ?string $notes = null): array
    {
        if (!Schema::hasTable('fiscal_periods')) {
            return ['status' => false, 'message' => 'Tabel periode fiskal belum tersedia pada database.'];
        }

        $period = DB::table('fiscal_periods')->where('id', $periodId)->first();
        if (!$period) {
            return ['status' => false, 'message' => 'Periode akuntansi tidak ditemukan.'];
        }

        $updateData = [
            'status'     => $status,
            'updated_at' => now(),
        ];

        if (in_array($status, ['locked', 'closed'])) {
            $updateData['closed_by'] = $userId;
            $updateData['closed_at'] = now();
        }

        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }

        DB::table('fiscal_periods')->where('id', $periodId)->update($updateData);

        return [
            'status'  => true,
            'message' => "Status periode akuntansi '{$period->name}' berhasil diperbarui menjadi {$status}."
        ];
    }

    /**
     * Eksekusi Tutup Buku Tahunan (Year-End Closing) dengan memindahkan Laba Bersih ke Laba Ditahan.
     *
     * @param int $storeId
     * @param int $year
     * @param int $userId
     * @return array
     */
    public function closeFiscalYear(int $storeId, int $year, int $userId): array
    {
        if (!Schema::hasTable('fiscal_periods') || !Schema::hasTable('account_transactions')) {
            return ['status' => false, 'message' => 'Tabel akuntansi atau periode fiskal belum tersedia pada database.'];
        }

        return DB::transaction(function () use ($storeId, $year, $userId) {
            $startDate = "{$year}-01-01";
            $endDate   = "{$year}-12-31";

            // 1. Dapatkan Pengaturan Akun Ekuitas / Laba Ditahan (Retained Earnings)
            $settings = AccountSetting::withoutGlobalScopes()->where('store_id', $storeId)->first()
                ?? AccountSetting::withoutGlobalScopes()->whereNull('store_id')->first();

            $retainedEarningsAccount = null;
            if ($settings && !empty($settings->retained_earning_account)) {
                $retainedEarningsAccount = is_numeric($settings->retained_earning_account)
                    ? Account::withoutGlobalScopes()->find($settings->retained_earning_account)
                    : $settings->retained_earning_account;
            }
            if (!$retainedEarningsAccount) {
                // Fallback cari akun bertipe ekuitas dengan nama mengandung 'Laba Ditahan' / 'Retained Earning' / 'Modal'
                $retainedEarningsAccount = Account::withoutGlobalScopes()
                    ->where(function ($q) {
                        $q->where('name', 'like', '%Laba Ditahan%')
                          ->orWhere('name', 'like', '%Retained%')
                          ->orWhere('sub_type', 'equity')
                          ->orWhere('name', 'like', '%Modal%');
                    })
                    ->first();
            }

            if (!$retainedEarningsAccount) {
                return [
                    'status'  => false,
                    'message' => 'Tutup Buku Gagal: Akun Laba Ditahan (Retained Earnings) belum ditemukan pada Bagan Akun (Chart of Accounts).'
                ];
            }

            // 2. Hitung Total Akumulasi Pendapatan & Beban Tahun Berjalan
            $revenueTotal = DB::table('account_transactions')
                ->join('accounts', 'accounts.id', '=', 'account_transactions.account_id')
                ->whereBetween('account_transactions.operation_date', [$startDate, $endDate])
                ->whereIn('accounts.sub_type', ['revenue', 'pendapatan', 'sales'])
                ->select(DB::raw("SUM(CASE WHEN type = 'credit' THEN amount ELSE -amount END) as net_revenue"))
                ->value('net_revenue') ?? 0;

            $expenseTotal = DB::table('account_transactions')
                ->join('accounts', 'accounts.id', '=', 'account_transactions.account_id')
                ->whereBetween('account_transactions.operation_date', [$startDate, $endDate])
                ->whereIn('accounts.sub_type', ['expense', 'beban', 'cogs', 'biaya'])
                ->select(DB::raw("SUM(CASE WHEN type = 'debit' THEN amount ELSE -amount END) as net_expense"))
                ->value('net_expense') ?? 0;

            $netIncome = (float)$revenueTotal - (float)$expenseTotal;

            // 3. Buat Jurnal Penutup Pemindahan Laba Bersih ke Laba Ditahan
            $refNo = 'CLS-' . $year . '-' . date('YmdHis');
            if (abs($netIncome) > 0.01) {
                // Jika Laba Bersih Positif (Untung) -> Kredit Laba Ditahan
                // Jika Rugi Bersih (Rugi) -> Debit Laba Ditahan
                $entryType = $netIncome >= 0 ? 'credit' : 'debit';

                AccountTransaction::create([
                    'account_id'     => $retainedEarningsAccount->id,
                    'transaction_id' => null,
                    'created_by'     => $userId,
                    'amount'         => abs($netIncome),
                    'type'           => $entryType,
                    'sub_type'       => 'year_end_closing',
                    'ref_no'         => $refNo,
                    'operation_date' => $endDate,
                    'name'           => "Tutup Buku Akhir Tahun {$year} - Pemindahan Laba/Rugi Bersih",
                ]);
            }

            // 4. Kunci / Daftarkan Periode Fiskal Tahunan sebagai Closed
            DB::table('fiscal_periods')->updateOrInsert(
                [
                    'store_id'   => $storeId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                ],
                [
                    'name'       => "Tahun Buku {$year}",
                    'status'     => 'closed',
                    'closed_by'  => $userId,
                    'closed_at'  => now(),
                    'notes'      => "Tutup buku tahunan {$year}. Net Income: Rp " . number_format($netIncome, 2),
                    'updated_at' => now(),
                ]
            );

            Log::info("Fiscal Year {$year} closed successfully for Store ID {$storeId}. Net Income: {$netIncome}");

            return [
                'status'        => true,
                'net_income'    => $netIncome,
                'revenue_total' => (float)$revenueTotal,
                'expense_total' => (float)$expenseTotal,
                'ref_no'        => $refNo,
                'message'       => "Tutup Buku Tahun {$year} berhasil diselesaikan. Saldo laba bersih Rp " . number_format($netIncome) . " telah dipindahkan ke Laba Ditahan."
            ];
        });
    }
}
