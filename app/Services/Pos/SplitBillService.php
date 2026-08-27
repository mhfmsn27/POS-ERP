<?php

namespace App\Services\Pos;

use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use Illuminate\Support\Facades\DB;

class SplitBillService
{
    /**
     * Membagi total tagihan menjadi beberapa bagian rata (Split Bill by Equal Amount).
     *
     * @param int $transactionId
     * @param int $splitCount Jumlah orang/bagian
     * @return array
     */
    public function splitBillEqual(int $transactionId, int $splitCount = 2): array
    {
        $trx = Transaction::withoutGlobalScopes()->find($transactionId);
        if (!$trx) {
            return ['status' => false, 'message' => 'Transaksi tidak ditemukan.'];
        }

        $splits = max(1, $splitCount);
        $total = (float)$trx->final_total;
        $perPerson = round($total / $splits, 2);

        $breakdown = [];
        for ($i = 1; $i <= $splits; $i++) {
            $breakdown[] = [
                'person_number' => $i,
                'amount'        => $perPerson,
                'status'        => 'pending',
            ];
        }

        return [
            'status'         => true,
            'transaction_id' => $transactionId,
            'total_amount'   => $total,
            'split_count'    => $splits,
            'per_person'     => $perPerson,
            'breakdown'      => $breakdown
        ];
    }

    /**
     * Memproses pelunasan transaksi dengan multi-metode pembayaran (Multi-Tender Settlement).
     *
     * @param int $transactionId
     * @param array $payments Array of ['method' => 'cash|qris|card', 'amount' => 50000]
     * @return array
     */
    public function settleMultiTender(int $transactionId, array $payments): array
    {
        return DB::transaction(function () use ($transactionId, $payments) {
            $trx = Transaction::withoutGlobalScopes()->where('id', $transactionId)->lockForUpdate()->first();
            if (!$trx) {
                return ['status' => false, 'message' => 'Transaksi tidak ditemukan.'];
            }

            $totalPaid = 0;
            $paymentRecords = [];

            foreach ($payments as $p) {
                $amt = abs((float)($p['amount'] ?? 0));
                $method = $p['method'] ?? 'cash';

                if ($amt > 0) {
                    $record = TransactionPayment::create([
                        'transaction_id' => $trx->id,
                        'amount'         => $amt,
                        'method'         => $method,
                        'payment_status' => 'paid',
                    ]);

                    $paymentRecords[] = [
                        'payment_id' => $record->id,
                        'method'     => $method,
                        'amount'     => $amt,
                    ];

                    $totalPaid += $amt;
                }
            }

            // Update status transaksi jika lunas
            $finalTotal = (float)$trx->final_total;
            $isPaid = ($totalPaid >= $finalTotal);

            $trx->update([
                'payment_status' => $isPaid ? 'paid' : 'due',
                'status'         => $isPaid ? 'final' : $trx->status,
            ]);

            // Award Loyalty Points if fully settled
            if ($isPaid && !empty($trx->customer_id)) {
                try {
                    app(\App\Services\Crm\CustomerLoyaltyService::class)->addPointsForSale(
                        (int)$trx->customer_id,
                        (int)$trx->store_id,
                        (int)$trx->id,
                        (float)$trx->final_total
                    );
                } catch (\Throwable $loyaltyEx) {}
            }

            return [
                'status'        => true,
                'total_billed'  => $finalTotal,
                'total_paid'    => $totalPaid,
                'remaining_due' => max(0, $finalTotal - $totalPaid),
                'is_settled'    => $isPaid,
                'payments'      => $paymentRecords,
                'message'       => $isPaid ? "Tagihan Rp " . number_format($finalTotal) . " LUNAS via Multi-Tender." : "Pembayaran parsial Rp " . number_format($totalPaid) . " berhasil dicatat."
            ];
        });
    }
}
