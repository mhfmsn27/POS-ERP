<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WarehouseBinService
{
    /**
     * Mendaftarkan lokasi Rak/Bin/Lorong Gudang baru.
     *
     * @param int $storeId
     * @param int|null $warehouseId
     * @param string $zone
     * @param string $aisle
     * @param string $rack
     * @param string $shelf
     * @param string|null $description
     * @return array
     */
    public function createBin(int $storeId, ?int $warehouseId, string $zone, string $aisle, string $rack, string $shelf, ?string $description = null): array
    {
        if (!Schema::hasTable('warehouse_bin_locations')) {
            return ['status' => false, 'message' => 'Tabel lokasi bin belum aktif.'];
        }

        $binCode = strtoupper("{$zone}-{$aisle}-{$rack}-{$shelf}");

        $exists = DB::table('warehouse_bin_locations')->where('bin_code', $binCode)->exists();
        if ($exists) {
            return ['status' => false, 'message' => "Kode Bin {$binCode} sudah terdaftar."];
        }

        $id = DB::table('warehouse_bin_locations')->insertGetId([
            'store_id'     => $storeId,
            'warehouse_id' => $warehouseId,
            'zone'         => strtoupper($zone),
            'aisle'        => strtoupper($aisle),
            'rack'         => strtoupper($rack),
            'shelf'        => strtoupper($shelf),
            'bin_code'     => $binCode,
            'description'  => $description,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return [
            'status'   => true,
            'bin_id'   => $id,
            'bin_code' => $binCode,
            'message'  => "Lokasi Bin Gudang {$binCode} berhasil dibuat."
        ];
    }

    /**
     * Mengambil daftar seluruh lokasi Rak & Bin Gudang.
     *
     * @param int|null $storeId
     * @return array
     */
    public function getBins(?int $storeId = null): array
    {
        if (!Schema::hasTable('warehouse_bin_locations')) {
            return ['status' => true, 'bins' => []];
        }

        $query = DB::table('warehouse_bin_locations');
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $bins = $query->orderBy('bin_code', 'asc')->get();

        return [
            'status'     => true,
            'total_bins' => count($bins),
            'bins'       => $bins
        ];
    }
}
