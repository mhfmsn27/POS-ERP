<?php

namespace App\Services\Pos;

use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfflinePosSyncService
{
    /**
     * Menerima dan menyinkronkan transaksi kasir yang disimpan di IndexedDB saat offline.
     *
     * @param array $offlineTransactions
     * @param int $storeId
     * @param int $userId
     * @return array
     */
    public function syncTransactions(array $offlineTransactions, int $storeId, int $userId): array
    {
        $synced = 0;
        $errors = [];

        foreach ($offlineTransactions as $trxData) {
            $offlineUuid = $trxData['offline_uuid'] ?? null;
            if (!$offlineUuid) {
                $offlineUuid = 'OFFLINE-' . uniqid();
            }

            // Cek apakah transaksi sudah pernah disinkronkan sebelumnya (Idempotent)
            $exists = Transaction::withoutGlobalScopes()
                ->where('ref_no', $offlineUuid)
                ->first();

            if ($exists) {
                $synced++;
                continue;
            }

            try {
                DB::transaction(function () use ($trxData, $offlineUuid, $storeId, $userId, &$synced) {
                    $finalTotal = (float)($trxData['final_total'] ?? 0);

                    $transaction = Transaction::withoutGlobalScopes()->create([
                        'store_id'         => $storeId,
                        'type'             => 'sell',
                        'status'           => 'final',
                        'payment_status'   => 'paid',
                        'customer_id'      => $trxData['customer_id'] ?? 1,
                        'ref_no'           => $offlineUuid,
                        'invoice_no'       => $trxData['invoice_no'] ?? $offlineUuid,
                        'transaction_date' => $trxData['created_at'] ?? now(),
                        'total_before_tax' => $finalTotal,
                        'tax_final'        => 0,
                        'final_total'      => $finalTotal,
                        'created_by'       => $userId,
                        'type_sell'        => 'pos_offline_synced',
                    ]);

                    // Simpan items
                    $items = $trxData['items'] ?? [];
                    foreach ($items as $item) {
                        Sell::create([
                            'transaction_id' => $transaction->id,
                            'store_id'       => $storeId,
                            'product_id'     => $item['product_id'] ?? 1,
                            'variation_id'   => $item['variation_id'] ?? null,
                            'qty'            => $item['quantity'] ?? 1,
                            'unit_qty'       => $item['quantity'] ?? 1,
                            'unit_price'     => $item['unit_price'] ?? 0,
                            'unit_price_before_disc' => $item['unit_price'] ?? 0,
                        ]);
                    }

                    // Simpan pembayaran
                    TransactionPayment::create([
                        'transaction_id' => $transaction->id,
                        'amount'         => $finalTotal,
                        'method'         => $trxData['payment_method'] ?? 'cash',
                        'payment_status' => 'paid',
                    ]);

                    // Enterprise Loyalty Points Auto-Credit for Offline Synced Transaction
                    try {
                        if (!empty($transaction->customer_id)) {
                            app(\App\Services\Crm\CustomerLoyaltyService::class)->addPointsForSale(
                                (int)$transaction->customer_id,
                                (int)$transaction->store_id,
                                (int)$transaction->id,
                                (float)$transaction->final_total
                            );
                        }
                    } catch (\Throwable $loyaltyEx) {}

                    // Enterprise FEFO Batch Stock Auto-Deduction
                    try {
                        $batchService = app(\App\Services\Inventory\BatchExpiryService::class);
                        foreach ($items as $item) {
                            $vId = $item['variation_id'] ?? null;
                            $sQty = $item['quantity'] ?? 1;
                            if (!empty($vId) && (float)$sQty > 0) {
                                $batchService->deductStockFEFO((int)$vId, (int)$transaction->store_id, (float)$sQty);
                            }
                        }
                    } catch (\Throwable $batchEx) {}

                    $synced++;
                });
            } catch (\Throwable $e) {
                $errors[] = "Error pada transaksi {$offlineUuid}: " . $e->getMessage();
                Log::warning("Offline POS Sync failed for {$offlineUuid}: " . $e->getMessage());
            }
        }

        return [
            'status'        => count($errors) === 0,
            'synced_count'  => $synced,
            'total_payload' => count($offlineTransactions),
            'errors'        => $errors,
            'message'       => "Sinkronisasi selesai: {$synced} transaksi offline berhasil diproses ke server."
        ];
    }
}
