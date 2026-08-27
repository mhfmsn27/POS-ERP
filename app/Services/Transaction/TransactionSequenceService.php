<?php

namespace App\Services\Transaction;

use App\Helper;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionSequenceService
{
    /**
     * Generate a unique, race-condition-proof reference number.
     *
     * @param string $prefix (e.g. 'SL', 'PO', 'SR', 'TR')
     * @param string $type (e.g. 'sell', 'purchase', 'sale_return')
     * @param int|string|null $storeId
     * @return array ['invoice_no' => '00001', 'ref_no' => 'SL2608/00001']
     */
    public function generateNextReference(string $prefix = 'SL', string $type = 'sell', $storeId = null): array
    {
        $today = Carbon::today();
        $datePrefix = date('ym');

        // Cari transaksi terakhir pada hari/tahun ini dengan lockForUpdate untuk mencegah race condition
        $lastTransaction = Transaction::withoutGlobalScopes()
            ->where('type', $type)
            ->whereDate('created_at', $today)
            ->when(!empty($storeId), function ($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first(['id', 'invoice_no', 'ref_no']);

        $nextNumber = 1;

        if ($lastTransaction && !empty($lastTransaction->invoice_no) && is_numeric($lastTransaction->invoice_no)) {
            $nextNumber = (int)$lastTransaction->invoice_no + 1;
        } else {
            // Fallback: Hitung transaksi hari ini
            $countToday = Transaction::withoutGlobalScopes()
                ->where('type', $type)
                ->whereDate('created_at', $today)
                ->when(!empty($storeId), function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                })
                ->count();
            $nextNumber = $countToday + 1;
        }

        $invoiceNumber = sprintf("%05d", $nextNumber);
        $refNo = Helper::transactionKey($prefix, $invoiceNumber);

        // Anti-collision guard: Jika nomor faktur ini sudah ada (misal akibat import/restore), terus naikkan
        $attempts = 0;
        while (Transaction::withoutGlobalScopes()->where('ref_no', $refNo)->exists() && $attempts < 50) {
            $nextNumber++;
            $invoiceNumber = sprintf("%05d", $nextNumber);
            $refNo = Helper::transactionKey($prefix, $invoiceNumber);
            $attempts++;
        }

        return [
            'invoice_no' => $invoiceNumber,
            'ref_no'     => $refNo,
        ];
    }
}
