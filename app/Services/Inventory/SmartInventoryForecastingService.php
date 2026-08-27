<?php

namespace App\Services\Inventory;

use App\Models\Product\Stock;
use App\Models\Product\Variation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SmartInventoryForecastingService
{
    /**
     * Menghitung laju penjualan (velocity) dan prediksi hari stok habis per produk di suatu toko.
     *
     * @param int|null $storeId
     * @param int $lookbackDays Default 30 hari
     * @return array
     */
    public function getInventoryForecast(?int $storeId = null, int $lookbackDays = 30): array
    {
        $startDate = now()->subDays($lookbackDays)->format('Y-m-d');
        $endDate   = now()->format('Y-m-d');

        // 1. Ambil data penjualan per variasi produk dalam X hari terakhir
        $salesQuery = DB::table('sells')
            ->join('transactions', 'transactions.id', '=', 'sells.transaction_id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->whereNull('sells.deleted_at')
            ->whereNull('transactions.deleted_at')
            ->whereBetween('transactions.created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);

        if ($storeId) {
            $salesQuery->where('transactions.store_id', $storeId);
        }

        $salesData = $salesQuery->select(
            'sells.variation_id',
            DB::raw('SUM(sells.qty - IFNULL(sells.qty_return, 0)) as total_qty_sold')
        )->groupBy('sells.variation_id')
        ->pluck('total_qty_sold', 'variation_id')
        ->toArray();

        // 2. Ambil stok aktif saat ini
        $stockQuery = DB::table('stocks')
            ->join('variations', 'variations.id', '=', 'stocks.variation_id')
            ->join('products', 'products.id', '=', 'variations.product_id');

        if ($storeId) {
            $stockQuery->where('stocks.store_id', $storeId);
        }

        $stocks = $stockQuery->select(
            'stocks.id as stock_id',
            'stocks.store_id',
            'stocks.variation_id',
            'stocks.product_id',
            'stocks.qty as current_stock',
            'products.name as product_name',
            'variations.name as variation_name',
            'variations.default_purchase_price as purchase_price',
            'variations.default_sell_price as sell_price'
        )->get();

        // 3. Ambil konfigurasi reorder jika tabel tersedia
        $reorderSettings = [];
        if (Schema::hasTable('inventory_reorder_settings')) {
            $rQuery = DB::table('inventory_reorder_settings');
            if ($storeId) {
                $rQuery->where('store_id', $storeId);
            }
            $reorderSettings = $rQuery->get()->keyBy('variation_id')->toArray();
        }

        $forecastList = [];
        $criticalCount = 0;
        $warningCount = 0;

        foreach ($stocks as $stock) {
            $vId = $stock->variation_id;
            $soldQty = (float)($salesData[$vId] ?? 0);
            $dailyVelocity = $soldQty > 0 ? round($soldQty / $lookbackDays, 2) : 0.0;
            $currentStock = (float)$stock->current_stock;

            $setting = $reorderSettings[$vId] ?? null;
            $safetyStock = $setting ? (float)$setting->safety_stock : 10.0;
            $leadTimeDays = $setting ? (int)$setting->lead_time_days : 3;
            $minReorderQty = $setting ? (float)$setting->min_reorder_qty : 20.0;

            // Hitung sisa hari stok sebelum habis (Days of Inventory Remaining)
            if ($dailyVelocity > 0) {
                $daysRemaining = round($currentStock / $dailyVelocity, 1);
            } else {
                $daysRemaining = $currentStock > 0 ? 999 : 0; // Tidak ada pergerakan penjualan
            }

            // Hitung Titik Pemesanan Ulang (Reorder Point)
            $reorderPoint = ($leadTimeDays * $dailyVelocity) + $safetyStock;
            $suggestedReorderQty = 0;

            if ($currentStock <= $reorderPoint && $dailyVelocity > 0) {
                $deficit = $reorderPoint - $currentStock;
                $suggestedReorderQty = max($minReorderQty, ceil($deficit + $safetyStock));
            }

            // Tentukan Tingkat Risiko Stok
            if ($currentStock <= 0) {
                $riskLevel = 'OUT_OF_STOCK';
                $criticalCount++;
            } elseif ($daysRemaining <= $leadTimeDays) {
                $riskLevel = 'CRITICAL_STOCKOUT';
                $criticalCount++;
            } elseif ($currentStock <= $reorderPoint) {
                $riskLevel = 'WARNING_REORDER';
                $warningCount++;
            } elseif ($daysRemaining > 90) {
                $riskLevel = 'OVERSTOCKED';
            } else {
                $riskLevel = 'HEALTHY';
            }

            $forecastList[] = [
                'variation_id'          => $vId,
                'product_id'            => $stock->product_id,
                'product_name'          => $stock->product_name,
                'variation_name'        => $stock->variation_name,
                'current_stock'         => $currentStock,
                'sold_last_30_days'     => $soldQty,
                'daily_velocity'        => $dailyVelocity,
                'days_remaining'        => $daysRemaining,
                'reorder_point'         => round($reorderPoint, 2),
                'suggested_reorder_qty' => $suggestedReorderQty,
                'purchase_price'        => (float)$stock->purchase_price,
                'estimated_reorder_cost'=> $suggestedReorderQty * (float)$stock->purchase_price,
                'risk_level'            => $riskLevel,
            ];
        }

        // Urutkan prioritas dari yang paling kritis (sisa hari paling sedikit)
        usort($forecastList, function ($a, $b) {
            return $a['days_remaining'] <=> $b['days_remaining'];
        });

        return [
            'status'         => true,
            'lookback_days'  => $lookbackDays,
            'critical_items' => $criticalCount,
            'warning_items'  => $warningCount,
            'total_items'    => count($forecastList),
            'items'          => $forecastList,
        ];
    }
}
