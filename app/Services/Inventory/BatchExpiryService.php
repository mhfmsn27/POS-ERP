<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BatchExpiryService
{
    /**
     * Mendaftarkan batch / lot baru dengan tanggal kadaluarsa.
     *
     * @param array $data
     * @return array
     */
    public function createBatch(array $data): array
    {
        if (!Schema::hasTable('product_batches')) {
            return ['status' => false, 'message' => 'Tabel product_batches belum tersedia.'];
        }

        $batchId = DB::table('product_batches')->insertGetId([
            'product_id'        => $data['product_id'],
            'variation_id'      => $data['variation_id'],
            'store_id'          => $data['store_id'] ?? my_store() ?? 1,
            'batch_number'      => $data['batch_number'],
            'manufactured_date' => $data['manufactured_date'] ?? null,
            'expiry_date'       => $data['expiry_date'],
            'initial_qty'       => (float)$data['qty'],
            'current_qty'       => (float)$data['qty'],
            'cost_price'        => (float)($data['cost_price'] ?? 0),
            'status'            => 'active',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return [
            'status'   => true,
            'batch_id' => $batchId,
            'message'  => "Nomor batch {$data['batch_number']} berhasil didaftarkan."
        ];
    }

    /**
     * Mendapatkan daftar produk yang mendekati tanggal kadaluarsa (FEFO Monitoring).
     *
     * @param int|null $storeId
     * @param int $thresholdDays Batas hari (default 60 hari)
     * @return array
     */
    public function getExpiringBatches(?int $storeId = null, int $thresholdDays = 60): array
    {
        if (!Schema::hasTable('product_batches')) {
            return ['status' => true, 'total' => 0, 'batches' => []];
        }

        $today = now()->format('Y-m-d');
        $warningLimit = now()->addDays($thresholdDays)->format('Y-m-d');

        $query = DB::table('product_batches')
            ->join('products', 'products.id', '=', 'product_batches.product_id')
            ->join('variations', 'variations.id', '=', 'product_batches.variation_id')
            ->where('product_batches.current_qty', '>', 0)
            ->where('product_batches.expiry_date', '<=', $warningLimit);

        if ($storeId) {
            $query->where('product_batches.store_id', $storeId);
        }

        $batches = $query->select(
            'product_batches.*',
            'products.name as product_name',
            'variations.name as variation_name',
            'variations.default_sell_price as sell_price'
        )->orderBy('product_batches.expiry_date', 'asc')->get();

        $result = [];
        foreach ($batches as $b) {
            $expiryDate = \Carbon\Carbon::parse($b->expiry_date);
            $daysLeft = (int) now()->startOfDay()->diffInDays($expiryDate, false);

            if ($daysLeft < 0) {
                $status = 'EXPIRED';
                $discount = 0.00;
                $action = 'Tarik dari rak / Buang';
            } elseif ($daysLeft <= 14) {
                $status = 'CRITICAL_NEAR_EXPIRY';
                $discount = 0.50; // 50% Auto Markdown
                $action = 'Flash Sale Diskon 50%';
            } elseif ($daysLeft <= 30) {
                $status = 'WARNING_NEAR_EXPIRY';
                $discount = 0.30; // 30% Auto Markdown
                $action = 'Promo Cuci Gudang Diskon 30%';
            } else {
                $status = 'MONITORED';
                $discount = 0.10; // 10% Early Markdown
                $action = 'Prioritaskan Penjualan (FEFO)';
            }

            $result[] = [
                'batch_id'         => $b->id,
                'batch_number'     => $b->batch_number,
                'product_name'     => $b->product_name,
                'variation_name'   => $b->variation_name,
                'expiry_date'      => $b->expiry_date,
                'days_left'        => $daysLeft,
                'current_qty'      => (float)$b->current_qty,
                'sell_price'       => (float)$b->sell_price,
                'status'           => $status,
                'markdown_discount'=> $discount,
                'recommended_action'=> $action,
            ];
        }

        return [
            'status'  => true,
            'total'   => count($result),
            'batches' => $result,
        ];
    }

    /**
     * Memotong stok batch menggunakan metode FEFO (First Expired First Out).
     *
     * @param int $variationId
     * @param int $storeId
     * @param float $qtyToDeduct
     * @return float Kuantitas yang berhasil dipotong
     */
    public function deductStockFEFO(int $variationId, int $storeId, float $qtyToDeduct): float
    {
        if (!Schema::hasTable('product_batches') || $qtyToDeduct <= 0) {
            return 0;
        }

        $batches = DB::table('product_batches')
            ->where('variation_id', $variationId)
            ->where('store_id', $storeId)
            ->where('current_qty', '>', 0)
            ->orderBy('expiry_date', 'asc') // Urutan pertama yang paling cepat kadaluarsa
            ->lockForUpdate()
            ->get();

        $remaining = $qtyToDeduct;
        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $deduct = min((float)$batch->current_qty, $remaining);
            $newQty = (float)$batch->current_qty - $deduct;
            $status = $newQty <= 0 ? 'depleted' : 'active';

            DB::table('product_batches')->where('id', $batch->id)->update([
                'current_qty' => $newQty,
                'status'      => $status,
                'updated_at'  => now(),
            ]);

            $remaining -= $deduct;
        }

        return $qtyToDeduct - $remaining;
    }
}
