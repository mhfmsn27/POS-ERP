<?php

namespace App\Services\Ecommerce;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FlashSaleService
{
    /**
     * Mendaftarkan kampanye Flash Sale baru.
     *
     * @param int $storeId
     * @param int $productId
     * @param int|null $variationId
     * @param string $name
     * @param float $originalPrice
     * @param float $flashPrice
     * @param int $quotaTotal
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    public function createCampaign(
        int $storeId,
        int $productId,
        ?int $variationId,
        string $name,
        float $originalPrice,
        float $flashPrice,
        int $quotaTotal,
        string $startTime,
        string $endTime
    ): array {
        if (!Schema::hasTable('ecommerce_flash_sales')) {
            return ['status' => false, 'message' => 'Tabel flash sale belum tersedia.'];
        }

        $id = DB::table('ecommerce_flash_sales')->insertGetId([
            'store_id'       => $storeId,
            'product_id'     => $productId,
            'variation_id'   => $variationId,
            'name'           => $name,
            'original_price' => $originalPrice,
            'flash_price'    => $flashPrice,
            'quota_total'    => $quotaTotal,
            'quota_sold'     => 0,
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'is_active'      => true,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return [
            'status'      => true,
            'campaign_id' => $id,
            'message'     => "Kampanye Flash Sale '{$name}' berhasil dibuat."
        ];
    }

    /**
     * Mengambil daftar produk Flash Sale yang sedang aktif saat ini lengkap dengan countdown timer.
     *
     * @param int|null $storeId
     * @return array
     */
    public function getActiveFlashSales(?int $storeId = null): array
    {
        if (!Schema::hasTable('ecommerce_flash_sales')) {
            return ['status' => true, 'flash_sales' => []];
        }

        $now = now();
        $query = DB::table('ecommerce_flash_sales')
            ->where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->whereRaw('quota_sold < quota_total');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $sales = $query->orderBy('id', 'desc')->get();

        $formatted = [];
        foreach ($sales as $s) {
            $endCarbon = \Carbon\Carbon::parse($s->end_time);
            $remainingSeconds = max(0, $now->diffInSeconds($endCarbon, false));

            $discountPercent = $s->original_price > 0
                ? round((($s->original_price - $s->flash_price) / $s->original_price) * 100)
                : 0;

            $formatted[] = [
                'id'                => $s->id,
                'name'              => $s->name,
                'product_id'        => $s->product_id,
                'variation_id'      => $s->variation_id,
                'original_price'    => (float)$s->original_price,
                'flash_price'       => (float)$s->flash_price,
                'discount_percent'  => $discountPercent,
                'quota_total'       => (int)$s->quota_total,
                'quota_sold'        => (int)$s->quota_sold,
                'quota_remaining'   => (int)($s->quota_total - $s->quota_sold),
                'remaining_seconds' => $remainingSeconds,
                'end_time'          => $s->end_time,
            ];
        }

        return [
            'status'      => true,
            'total_items' => count($formatted),
            'flash_sales' => $formatted
        ];
    }

    /**
     * Memeriksa dan menerapkan harga promo jika produk sedang berada dalam periode flash sale.
     *
     * @param int $storeId
     * @param int $productId
     * @param int|null $variationId
     * @param float $regularPrice
     * @return array [is_flash: bool, price: float, campaign_id: int|null]
     */
    public function getEffectivePrice(int $storeId, int $productId, ?int $variationId, float $regularPrice): array
    {
        if (!Schema::hasTable('ecommerce_flash_sales')) {
            return ['is_flash' => false, 'price' => $regularPrice, 'campaign_id' => null];
        }

        $now = now();
        $campaign = DB::table('ecommerce_flash_sales')
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->whereRaw('quota_sold < quota_total')
            ->first();

        if ($campaign) {
            return [
                'is_flash'    => true,
                'price'       => (float)$campaign->flash_price,
                'campaign_id' => $campaign->id
            ];
        }

        return ['is_flash' => false, 'price' => $regularPrice, 'campaign_id' => null];
    }
}
