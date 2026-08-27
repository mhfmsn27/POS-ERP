<?php

namespace App\Services\Pos;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TableManagementService
{
    /**
     * Mendaftarkan nomor meja baru dalam restoran.
     *
     * @param int $storeId
     * @param string $tableNumber
     * @param int $capacity
     * @param string $zone
     * @return array
     */
    public function createTable(int $storeId, string $tableNumber, int $capacity = 4, string $zone = 'Main Hall'): array
    {
        if (!Schema::hasTable('restaurant_tables')) {
            return ['status' => false, 'message' => 'Tabel restaurant_tables belum aktif.'];
        }

        $existing = DB::table('restaurant_tables')
            ->where('store_id', $storeId)
            ->where('table_number', $tableNumber)
            ->first();

        if ($existing) {
            DB::table('restaurant_tables')->where('id', $existing->id)->update([
                'capacity'   => $capacity,
                'zone'       => $zone,
                'updated_at' => now(),
            ]);
            $id = $existing->id;
        } else {
            $id = DB::table('restaurant_tables')->insertGetId([
                'store_id'     => $storeId,
                'table_number' => $tableNumber,
                'capacity'     => $capacity,
                'zone'         => $zone,
                'status'       => 'available',
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return [
            'status'   => true,
            'table_id' => $id,
            'message'  => "Meja {$tableNumber} (Kapasitas {$capacity} orang) berhasil disimpan."
        ];
    }

    /**
     * Mengambil daftar seluruh meja beserta status okupansi real-time.
     *
     * @param int $storeId
     * @return array
     */
    public function getTables(int $storeId): array
    {
        if (!Schema::hasTable('restaurant_tables')) {
            return ['status' => true, 'tables' => []];
        }

        $tables = DB::table('restaurant_tables')
            ->where('store_id', $storeId)
            ->orderBy('table_number', 'asc')
            ->get();

        return [
            'status'       => true,
            'total_tables' => count($tables),
            'tables'       => $tables
        ];
    }

    /**
     * Memperbarui status meja (available, occupied, billed, reserved).
     *
     * @param int $tableId
     * @param string $status
     * @param int|null $transactionId
     * @return array
     */
    public function updateTableStatus(int $tableId, string $status, ?int $transactionId = null): array
    {
        if (!Schema::hasTable('restaurant_tables')) {
            return ['status' => false, 'message' => 'Tabel belum aktif.'];
        }

        DB::table('restaurant_tables')->where('id', $tableId)->update([
            'status'                 => $status,
            'current_transaction_id' => ($status === 'available') ? null : $transactionId,
            'updated_at'             => now(),
        ]);

        return [
            'status'     => true,
            'table_id'   => $tableId,
            'new_status' => $status,
            'message'    => "Status meja berhasil diubah menjadi {$status}."
        ];
    }
}
