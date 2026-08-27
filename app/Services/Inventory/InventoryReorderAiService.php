<?php

namespace App\Services\Inventory;

use App\Models\Product\Product;
use App\Models\Product\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryReorderAiService
{
    /**
     * Menghitung Rata-rata Penjualan Harian, Safety Stock, Reorder Point (ROP), dan Kuantitas Pesan Ekonomis (EOQ).
     *
     * @param int $storeId
     * @param int $leadTimeDays Rata-rata waktu tunggu pengiriman supplier (hari)
     * @return array
     */
    public function generateReorderRecommendations(int $storeId, int $leadTimeDays = 7): array
    {
        if (!Schema::hasTable('inventory_reorder_recommendations')) {
            return ['status' => false, 'message' => 'Tabel rekomendasi reorder belum aktif.'];
        }

        // Ambil data penjualan 30 hari terakhir
        $thirtyDaysAgo = now()->subDays(30);

        $salesVelocity = DB::table('sells')
            ->join('transactions', 'transactions.id', '=', 'sells.transaction_id')
            ->where('transactions.store_id', $storeId)
            ->where('transactions.created_at', '>=', $thirtyDaysAgo)
            ->selectRaw('sells.product_id, sells.variation_id, sum(sells.qty) as total_sold')
            ->groupBy('sells.product_id', 'sells.variation_id')
            ->get();

        $generatedCount = 0;

        foreach ($salesVelocity as $sale) {
            $dailyVelocity = (float)$sale->total_sold / 30.0;
            if ($dailyVelocity <= 0) continue;

            // Safety Stock = 3 hari buffer
            $safetyStock = round($dailyVelocity * 3, 2);
            // Reorder Point = (Daily Velocity * Lead Time) + Safety Stock
            $reorderPoint = round(($dailyVelocity * $leadTimeDays) + $safetyStock, 2);

            // Cek stok saat ini di database
            $stockQuery = Stock::withoutGlobalScopes()
                ->where('store_id', $storeId)
                ->where('product_id', $sale->product_id);

            if ($sale->variation_id) {
                $stockQuery->where('variation_id', $sale->variation_id);
            }

            $currentStock = (float) $stockQuery->sum('qty_available');

            // Jika stok saat ini <= Reorder Point, buat rekomendasi pemesanan
            if ($currentStock <= $reorderPoint) {
                // EOQ Estimasi = Kebutuhan 30 hari + Safety Stock
                $recommendedQty = max(10, round(($dailyVelocity * 30) + $safetyStock - $currentStock));

                DB::table('inventory_reorder_recommendations')->updateOrInsert(
                    [
                        'store_id'     => $storeId,
                        'product_id'   => $sale->product_id,
                        'variation_id' => $sale->variation_id,
                        'status'       => 'pending',
                    ],
                    [
                        'current_stock'         => $currentStock,
                        'safety_stock'          => $safetyStock,
                        'reorder_point'         => $reorderPoint,
                        'recommended_order_qty' => $recommendedQty,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]
                );

                $generatedCount++;
            }
        }

        return [
            'status'          => true,
            'generated_count' => $generatedCount,
            'message'         => "AI Reorder Engine: {$generatedCount} produk mencapai titik batas pemesanan ulang (ROP)."
        ];
    }

    /**
     * Mengambil daftar produk yang direkomendasikan untuk segera dipesan ke supplier.
     *
     * @param int $storeId
     * @return array
     */
    public function getPendingReorders(int $storeId): array
    {
        if (!Schema::hasTable('inventory_reorder_recommendations')) {
            return ['status' => true, 'reorders' => []];
        }

        $list = DB::table('inventory_reorder_recommendations')
            ->join('products', 'products.id', '=', 'inventory_reorder_recommendations.product_id')
            ->where('inventory_reorder_recommendations.store_id', $storeId)
            ->where('inventory_reorder_recommendations.status', 'pending')
            ->select(
                'inventory_reorder_recommendations.*',
                'products.name as product_name',
                'products.sku as product_sku'
            )
            ->orderBy('inventory_reorder_recommendations.current_stock', 'asc')
            ->get();

        return [
            'status'         => true,
            'total_pending'  => count($list),
            'recommendations'=> $list
        ];
    }
}
