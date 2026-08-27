<?php

namespace App\Services\Ecommerce;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WholesaleB2bService
{
    /**
     * Menetapkan harga grosir bertingkat (Dynamic Tier Pricing).
     *
     * @param int $storeId
     * @param int $productId
     * @param int|null $variationId
     * @param float $minQty
     * @param float|null $maxQty
     * @param float $tierPrice
     * @param string $customerGroup 'all', 'retail', 'reseller', 'agent', 'distributor'
     * @return array
     */
    public function setTierPrice(
        int $storeId,
        int $productId,
        ?int $variationId,
        float $minQty,
        ?float $maxQty,
        float $tierPrice,
        string $customerGroup = 'all'
    ): array {
        if (!Schema::hasTable('wholesale_tier_prices')) {
            return ['status' => false, 'message' => 'Tabel wholesale_tier_prices belum aktif.'];
        }

        DB::table('wholesale_tier_prices')->updateOrInsert(
            [
                'store_id'       => $storeId,
                'product_id'     => $productId,
                'variation_id'   => $variationId,
                'min_quantity'   => $minQty,
                'customer_group' => $customerGroup,
            ],
            [
                'max_quantity'   => $maxQty,
                'tier_price'     => $tierPrice,
                'updated_at'     => now(),
            ]
        );

        return [
            'status'   => true,
            'message'  => "Harga grosir Rp " . number_format($tierPrice) . " (Min: {$minQty} pcs) berhasil disimpan."
        ];
    }

    /**
     * Menghitung harga satuan efektif berdasarkan kuantitas beli dan grup pelanggan.
     *
     * @param int $storeId
     * @param int $productId
     * @param int|null $variationId
     * @param float $quantity
     * @param float $basePrice
     * @param string $customerGroup
     * @return float
     */
    public function calculateUnitPrice(
        int $storeId,
        int $productId,
        ?int $variationId,
        float $quantity,
        float $basePrice,
        string $customerGroup = 'all'
    ): float {
        if (!Schema::hasTable('wholesale_tier_prices') || $quantity <= 0) {
            return $basePrice;
        }

        $query = DB::table('wholesale_tier_prices')
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($q) use ($quantity) {
                $q->whereNull('max_quantity')->orWhere('max_quantity', '>=', $quantity);
            })
            ->where(function ($q) use ($customerGroup) {
                $q->where('customer_group', 'all')->orWhere('customer_group', $customerGroup);
            });

        if ($variationId) {
            $query->where(function ($q) use ($variationId) {
                $q->whereNull('variation_id')->orWhere('variation_id', $variationId);
            });
        }

        $tier = $query->orderBy('tier_price', 'asc')->first();

        return $tier ? (float)$tier->tier_price : $basePrice;
    }

    /**
     * Mengambil seluruh skema harga bertingkat untuk suatu produk.
     *
     * @param int $storeId
     * @param int $productId
     * @param int|null $variationId
     * @return array
     */
    public function getWholesaleTiers(int $storeId, int $productId, ?int $variationId = null): array
    {
        if (!Schema::hasTable('wholesale_tier_prices')) {
            return ['status' => true, 'tiers' => []];
        }

        $query = DB::table('wholesale_tier_prices')
            ->where('store_id', $storeId)
            ->where('product_id', $productId);

        if ($variationId) {
            $query->where(function ($q) use ($variationId) {
                $q->whereNull('variation_id')->orWhere('variation_id', $variationId);
            });
        }

        $tiers = $query->orderBy('min_quantity', 'asc')->get();

        return [
            'status' => true,
            'tiers'  => $tiers
        ];
    }
}
