<?php

namespace App\Services\Ecommerce;

use App\Models\Inventory\Stock;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EcommerceStockReservationService
{
    /**
     * Mencadangkan stok untuk pesanan online berstatus HOLD dengan pessimistic locking.
     * Mencegah terjadinya overselling saat flash sale atau checkout simultan.
     *
     * @param int $storeId
     * @param int $transactionId
     * @param array $items Array of ['product_id' => x, 'variation_id' => y, 'qty' => z]
     * @param int $holdMinutes Batas waktu pembayaran (default 60 menit)
     * @return array
     * @throws \Exception
     */
    public function reserveStock(int $storeId, int $transactionId, array $items, int $holdMinutes = 60): array
    {
        if (!Schema::hasTable('ecommerce_stock_reservations')) {
            return ['status' => true, 'message' => 'Tabel reservasi belum tersedia.'];
        }

        $expiresAt = now()->addMinutes($holdMinutes);
        $reservedRecords = [];

        foreach ($items as $item) {
            $productId   = (int)($item['product_id'] ?? 0);
            $variationId = (int)($item['variation_id'] ?? 0);
            $qty         = (int)($item['qty'] ?? 1);

            if ($productId <= 0 || $qty <= 0) {
                continue;
            }

            // Lock row stok fisik untuk mencegah race condition
            $stockRecord = Stock::withoutGlobalScopes()
                ->where('store_id', $storeId)
                ->where('product_id', $productId)
                ->where('variation_id', $variationId)
                ->lockForUpdate()
                ->first();

            $availableQty = $stockRecord ? (float)$stockRecord->qty : 0;

            if ($availableQty < $qty) {
                throw new \Exception("Stok tidak mencukupi untuk produk ID {$productId}. Tersedia: {$availableQty}, diminta: {$qty}");
            }

            // Kurangi stok riil saat di-hold
            $stockRecord->qty = $availableQty - $qty;
            $stockRecord->save();

            $id = DB::table('ecommerce_stock_reservations')->insertGetId([
                'store_id'       => $storeId,
                'transaction_id' => $transactionId,
                'product_id'     => $productId,
                'variation_id'   => $variationId,
                'quantity'       => $qty,
                'status'         => 'held',
                'expires_at'     => $expiresAt,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            $reservedRecords[] = $id;
        }

        return [
            'status'     => true,
            'expires_at' => $expiresAt->toIso8601String(),
            'reserved'   => count($reservedRecords),
            'message'    => 'Stok berhasil dicadangkan untuk checkout online.'
        ];
    }

    /**
     * Mengubah status reservasi menjadi COMMITTED saat pembayaran terkonfirmasi lunas.
     *
     * @param int $transactionId
     * @return bool
     */
    public function commitReservation(int $transactionId): bool
    {
        if (!Schema::hasTable('ecommerce_stock_reservations')) {
            return false;
        }

        DB::table('ecommerce_stock_reservations')
            ->where('transaction_id', $transactionId)
            ->where('status', 'held')
            ->update([
                'status'     => 'committed',
                'updated_at' => now(),
            ]);

        return true;
    }

    /**
     * Melepaskan kembali stok yang kadaluarsa (melewati batas waktu hold).
     *
     * @return int Jumlah reservasi yang berhasil dirilis kembali ke etalase
     */
    public function releaseExpiredReservations(): int
    {
        if (!Schema::hasTable('ecommerce_stock_reservations')) {
            return 0;
        }

        $expired = DB::table('ecommerce_stock_reservations')
            ->where('status', 'held')
            ->where('expires_at', '<', now())
            ->get();

        $releasedCount = 0;

        foreach ($expired as $res) {
            DB::transaction(function () use ($res, &$releasedCount) {
                // Kembalikan stok fisik ke tabel stocks dengan lock
                $stock = Stock::withoutGlobalScopes()
                    ->where('store_id', $res->store_id)
                    ->where('product_id', $res->product_id)
                    ->where('variation_id', $res->variation_id)
                    ->lockForUpdate()
                    ->first();

                if ($stock) {
                    $stock->qty = (float)$stock->qty + (float)$res->quantity;
                    $stock->save();
                }

                // Update status reservasi
                DB::table('ecommerce_stock_reservations')
                    ->where('id', $res->id)
                    ->update([
                        'status'     => 'released',
                        'updated_at' => now(),
                    ]);

                // Batalkan transaksi pesanan jika masih hold
                $trx = Transaction::withoutGlobalScopes()->find($res->transaction_id);
                if ($trx && $trx->payment_status === 'due' && in_array($trx->status, ['hold', 'pending'])) {
                    $trx->status = 'cancelled';
                    $trx->save();
                }

                $releasedCount++;
            });
        }

        if ($releasedCount > 0) {
            Log::info("[ECOMMERCE STOCK] Berhasil merilis {$releasedCount} reservasi stok yang kadaluarsa.");
        }

        return $releasedCount;
    }
}
