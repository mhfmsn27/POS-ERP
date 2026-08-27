<?php

namespace App\Services\Accounting;

use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BankReconciliationService
{
    /**
     * Mengimpor baris mutasi rekening koran bank ke dalam sistem.
     *
     * @param array $rows
     * @param int $storeId
     * @param int|null $accountId
     * @param string $bankName
     * @param int $userId
     * @return array
     */
    public function importStatement(array $rows, int $storeId, ?int $accountId, string $bankName, int $userId): array
    {
        if (!Schema::hasTable('bank_statement_logs')) {
            return ['status' => false, 'message' => 'Tabel bank_statement_logs belum tersedia.'];
        }

        $imported = 0;
        foreach ($rows as $row) {
            $amount = abs((float)($row['amount'] ?? 0));
            if ($amount <= 0) {
                continue;
            }

            $type = strtoupper($row['type'] ?? 'CR');
            if (!in_array($type, ['CR', 'DB'])) {
                $type = 'CR';
            }

            DB::table('bank_statement_logs')->insert([
                'store_id'         => $storeId,
                'account_id'       => $accountId,
                'bank_name'        => $bankName,
                'transaction_date' => $row['date'] ?? now()->format('Y-m-d'),
                'description'      => $row['description'] ?? 'Mutasi Bank',
                'type'             => $type,
                'amount'           => $amount,
                'balance_after'    => (float)($row['balance'] ?? 0),
                'status'           => 'unmatched',
                'imported_by'      => $userId,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $imported++;
        }

        return [
            'status'   => true,
            'imported' => $imported,
            'message'  => "Berhasil mengimpor {$imported} baris mutasi bank {$bankName}."
        ];
    }

    /**
     * Menjalankan mesin pencocokan cerdas antara mutasi bank dengan faktur penjualan belum lunas.
     *
     * @param int|null $storeId
     * @param int|null $accountId
     * @param int|null $userId
     * @return array
     */
    public function autoMatchTransactions(?int $storeId = null, ?int $accountId = null, ?int $userId = null): array
    {
        if (!Schema::hasTable('bank_statement_logs') || !Schema::hasTable('account_transactions')) {
            return ['status' => false, 'message' => 'Tabel rekonsiliasi belum tersedia.'];
        }

        $unmatchedQuery = DB::table('bank_statement_logs')
            ->where('status', 'unmatched')
            ->where('type', 'CR'); // Hanya dana masuk

        if ($storeId) {
            $unmatchedQuery->where('store_id', $storeId);
        }
        if ($accountId) {
            $unmatchedQuery->where('account_id', $accountId);
        }

        $unmatchedLogs = $unmatchedQuery->get();
        $matchedCount = 0;
        $totalMatchedAmount = 0;

        // Ambil pengaturan akun Piutang Usaha
        $settings = AccountSetting::withoutGlobalScopes()->where('store_id', $storeId)->first()
            ?? AccountSetting::withoutGlobalScopes()->whereNull('store_id')->first();

        $receivableAccount = null;
        if ($settings && !empty($settings->customer_debt)) {
            $receivableAccount = is_numeric($settings->customer_debt)
                ? Account::withoutGlobalScopes()->find($settings->customer_debt)
                : null;
        }
        if (!$receivableAccount) {
            $receivableAccount = Account::withoutGlobalScopes()
                ->where(function ($q) {
                    $q->where('name', 'like', '%Piutang%')
                      ->orWhere('sub_type', 'receivable');
                })->first();
        }

        $bankAccount = $accountId ? Account::withoutGlobalScopes()->find($accountId) : null;
        if (!$bankAccount) {
            $bankAccount = Account::withoutGlobalScopes()->where('sub_type', 'cash')->orWhere('name', 'like', '%Bank%')->first();
        }

        $matchedTrxIds = [];
        foreach ($unmatchedLogs as $log) {
            // Cari transaksi penjualan yang nominalnya sama persis atau ref_no ada pada deskripsi mutasi
            $trxQuery = Transaction::withoutGlobalScopes()
                ->where('type', 'sell')
                ->where('payment_status', 'due')
                ->whereNotIn('id', $matchedTrxIds)
                ->where(function ($q) use ($log) {
                    $q->where('final_total', $log->amount);
                    if (!empty($log->description) && strlen(trim($log->description)) >= 4) {
                        $cleanDesc = trim($log->description);
                        $q->orWhere('ref_no', 'like', "%{$cleanDesc}%");
                    }
                });

            if ($storeId) {
                $trxQuery->where('store_id', $storeId);
            }

            $candidateTrx = $trxQuery->first();

            if ($candidateTrx) {
                $matchedTrxIds[] = $candidateTrx->id;
                DB::transaction(function () use ($log, $candidateTrx, $bankAccount, $receivableAccount, $userId, &$matchedCount, &$totalMatchedAmount) {
                    $refNo = 'RECON-' . $log->id . '-' . date('Ymd');
                    $execUserId = $userId ?? auth()->id() ?? 1;

                    // 1. Catat Jurnal Pelunasan Piutang via Bank jika akun terkonfigurasi
                    if ($bankAccount && $receivableAccount) {
                        // Debit Kas Bank
                        AccountTransaction::create([
                            'account_id'     => $bankAccount->id,
                            'transaction_id' => $candidateTrx->id,
                            'created_by'     => $execUserId,
                            'amount'         => (float)$log->amount,
                            'type'           => 'debit',
                            'sub_type'       => 'bank_reconciliation',
                            'ref_no'         => $refNo,
                            'operation_date' => $log->transaction_date,
                            'name'           => "Penerimaan Bank Rekonsiliasi - Faktur #{$candidateTrx->ref_no}",
                        ]);

                        // Kredit Piutang Usaha
                        AccountTransaction::create([
                            'account_id'     => $receivableAccount->id,
                            'transaction_id' => $candidateTrx->id,
                            'created_by'     => $execUserId,
                            'amount'         => (float)$log->amount,
                            'type'           => 'credit',
                            'sub_type'       => 'bank_reconciliation',
                            'ref_no'         => $refNo,
                            'operation_date' => $log->transaction_date,
                            'name'           => "Pelunasan Piutang Rekonsiliasi - Faktur #{$candidateTrx->ref_no}",
                        ]);
                    }

                    // 2. Perbarui status pembayaran faktur menjadi LUNAS (paid)
                    $candidateTrx->payment_status = 'paid';
                    $candidateTrx->save();

                    // 3. Update status log mutasi bank menjadi MATCHED
                    DB::table('bank_statement_logs')->where('id', $log->id)->update([
                        'status'                 => 'matched',
                        'matched_transaction_id' => $candidateTrx->id,
                        'matched_notes'          => "Otomatis dicocokkan dengan Faktur #{$candidateTrx->ref_no} senilai Rp " . number_format($log->amount),
                        'updated_at'             => now(),
                    ]);

                    $matchedCount++;
                    $totalMatchedAmount += (float)$log->amount;
                });
            }
        }

        return [
            'status'               => true,
            'matched_count'        => $matchedCount,
            'total_matched_amount' => $totalMatchedAmount,
            'message'              => "Rekonsiliasi selesai: {$matchedCount} transaksi berhasil dicocokkan otomatis (Total: Rp " . number_format($totalMatchedAmount) . ")."
        ];
    }
}
