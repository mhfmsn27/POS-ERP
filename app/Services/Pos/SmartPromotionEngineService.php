<?php

namespace App\Services\Pos;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SmartPromotionEngineService
{
    /**
     * Membuat aturan promosi cerdas baru (Combo Bundle, BOGO, Threshold Discount).
     *
     * @param int $storeId
     * @param string $name
     * @param string $promoType 'combo_bundle', 'bogo', 'threshold_discount'
     * @param array $conditions
     * @param array $rewards
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function createPromotion(
        int $storeId,
        string $name,
        string $promoType,
        array $conditions,
        array $rewards,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        if (!Schema::hasTable('smart_promotions')) {
            return ['status' => false, 'message' => 'Tabel smart_promotions belum aktif.'];
        }

        $id = DB::table('smart_promotions')->insertGetId([
            'store_id'        => $storeId,
            'name'            => $name,
            'promo_type'      => $promoType,
            'conditions_json' => json_encode($conditions),
            'rewards_json'    => json_encode($rewards),
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'is_active'       => true,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return [
            'status'   => true,
            'promo_id' => $id,
            'message'  => "Promosi '{$name}' ({$promoType}) berhasil diaktifkan."
        ];
    }

    /**
     * Mengevaluasi keranjang belanja kasir/e-commerce dan menghitung diskon promosi terbaik.
     *
     * @param int $storeId
     * @param array $cartItems Array of ['product_id' => 1, 'quantity' => 2, 'unit_price' => 50000]
     * @param float $cartSubtotal
     * @return array
     */
    public function evaluateCart(int $storeId, array $cartItems, float $cartSubtotal): array
    {
        if (!Schema::hasTable('smart_promotions') || empty($cartItems)) {
            return [
                'total_discount' => 0,
                'final_total'    => $cartSubtotal,
                'applied_promos' => []
            ];
        }

        $today = now()->format('Y-m-d');
        $promotions = DB::table('smart_promotions')
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->get();

        $totalDiscount = 0;
        $appliedPromos = [];

        foreach ($promotions as $promo) {
            $conditions = json_decode($promo->conditions_json, true) ?? [];
            $rewards    = json_decode($promo->rewards_json, true) ?? [];

            // 1. Threshold Discount (Diskon belanja minimal Rp X)
            if ($promo->promo_type === 'threshold_discount') {
                $minSpend = (float)($conditions['min_spend'] ?? 0);
                if ($cartSubtotal >= $minSpend) {
                    $discountAmt = 0;
                    if (isset($rewards['discount_amount'])) {
                        $discountAmt = (float)$rewards['discount_amount'];
                    } elseif (isset($rewards['discount_percent'])) {
                        $discountAmt = round(($rewards['discount_percent'] / 100) * $cartSubtotal, 2);
                    }

                    if ($discountAmt > 0) {
                        $totalDiscount += $discountAmt;
                        $appliedPromos[] = [
                            'name'   => $promo->name,
                            'type'   => 'threshold_discount',
                            'saving' => $discountAmt,
                        ];
                    }
                }
            }
            // 2. Buy X Get Y (BOGO)
            elseif ($promo->promo_type === 'bogo') {
                $reqProdId = (int)($conditions['buy_product_id'] ?? 0);
                $reqQty    = (float)($conditions['buy_quantity'] ?? 1);

                foreach ($cartItems as $item) {
                    if ((int)$item['product_id'] === $reqProdId && (float)$item['quantity'] >= $reqQty) {
                        $freeQty = (float)($rewards['free_quantity'] ?? 1);
                        $discountAmt = $freeQty * (float)$item['unit_price'];

                        $totalDiscount += $discountAmt;
                        $appliedPromos[] = [
                            'name'   => $promo->name,
                            'type'   => 'bogo',
                            'saving' => $discountAmt,
                        ];
                    }
                }
            }
            // 3. Combo Bundle (Paket Kombo Hemat)
            elseif ($promo->promo_type === 'combo_bundle') {
                $requiredIds = $conditions['product_ids'] ?? [];
                if (!empty($requiredIds)) {
                    $cartProductIds = array_column($cartItems, 'product_id');
                    $containsAll = count(array_intersect($requiredIds, $cartProductIds)) === count($requiredIds);
                    if ($containsAll) {
                        $bundleDiscount = (float)($rewards['discount_amount'] ?? 0);
                        if ($bundleDiscount > 0) {
                            $totalDiscount += $bundleDiscount;
                            $appliedPromos[] = [
                                'name'   => $promo->name,
                                'type'   => 'combo_bundle',
                                'saving' => $bundleDiscount,
                            ];
                        }
                    }
                }
            }
        }

        $finalTotal = max(0, $cartSubtotal - $totalDiscount);

        return [
            'original_subtotal' => $cartSubtotal,
            'total_discount'    => $totalDiscount,
            'final_total'       => $finalTotal,
            'applied_promos'    => $appliedPromos
        ];
    }

    /**
     * Mengambil daftar promosi aktif di toko.
     *
     * @param int $storeId
     * @return array
     */
    public function getPromotions(int $storeId): array
    {
        if (!Schema::hasTable('smart_promotions')) {
            return ['status' => true, 'promotions' => []];
        }

        $promos = DB::table('smart_promotions')
            ->where('store_id', $storeId)
            ->orderBy('id', 'desc')
            ->get();

        return [
            'status'           => true,
            'total_promotions' => count($promos),
            'promotions'       => $promos
        ];
    }

    /**
     * Mengaktifkan / menonaktifkan aturan promosi.
     *
     * @param int $promoId
     * @param bool $isActive
     * @return array
     */
    public function togglePromotionStatus(int $promoId, bool $isActive): array
    {
        if (!Schema::hasTable('smart_promotions')) {
            return ['status' => false, 'message' => 'Tabel belum aktif.'];
        }

        DB::table('smart_promotions')->where('id', $promoId)->update([
            'is_active'  => $isActive,
            'updated_at' => now(),
        ]);

        return [
            'status'    => true,
            'promo_id'  => $promoId,
            'is_active' => $isActive,
            'message'   => "Status promosi berhasil diubah menjadi " . ($isActive ? 'Aktif' : 'Nonaktif') . "."
        ];
    }
}
