<?php

namespace App\Services\Cache;

use App\Models\Admin\Cuurency as Currency;
use App\Models\Admin\Setting;
use App\Models\Admin\Store;
use App\Models\Product\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MasterDataCacheService
{
    /**
     * Waktu simpan cache default (24 jam dalam detik).
     */
    const DEFAULT_TTL = 86400;

    /**
     * Dapatkan General Setting toko dari cache.
     *
     * @return Setting|null
     */
    public function getGeneralSetting(): ?Setting
    {
        return Cache::remember('master_data_general_setting', self::DEFAULT_TTL, function () {
            return Setting::first();
        });
    }

    /**
     * Dapatkan data Detail Toko dari cache.
     *
     * @param int|string $storeId
     * @return Store|null
     */
    public function getStoreDetail($storeId): ?Store
    {
        if (empty($storeId)) {
            return null;
        }

        $cacheKey = "master_data_store_{$storeId}";
        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($storeId) {
            return Store::withoutGlobalScopes()
                ->where('id', $storeId)
                ->first(['name', 'email', 'phone', 'address', 'tax_one', 'tax_two', 'tax_option', 'id', 'accountant_use', 'merchant_id']);
        });
    }

    /**
     * Dapatkan daftar kategori produk terindeks dari cache.
     *
     * @param int|string|null $storeId
     * @return Collection
     */
    public function getCategories($storeId = null): Collection
    {
        $cacheKey = $storeId ? "master_data_categories_store_{$storeId}" : 'master_data_categories_all';

        return Cache::remember($cacheKey, self::DEFAULT_TTL, function () use ($storeId) {
            $query = Category::orderBy('name', 'asc');
            if ($storeId) {
                $query->where('store_id', $storeId);
            }
            return $query->get(['id', 'name', 'parent_id', 'store_id']);
        });
    }

    /**
     * Bersihkan cache toko tertentu saat data diedit/diupdate.
     *
     * @param int|string $storeId
     * @return void
     */
    public function purgeStoreCache($storeId): void
    {
        Cache::forget("master_data_store_{$storeId}");
        Cache::forget("master_data_categories_store_{$storeId}");
    }

    /**
     * Bersihkan cache setting umum.
     *
     * @return void
     */
    public function purgeGeneralSetting(): void
    {
        Cache::forget('master_data_general_setting');
    }

    /**
     * Bersihkan seluruh cache master data.
     *
     * @return void
     */
    public function flushAll(): void
    {
        Cache::forget('master_data_general_setting');
        Cache::forget('master_data_categories_all');
    }
}
