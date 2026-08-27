<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SerialImeiTrackingService
{
    /**
     * Mendaftarkan Serial Number / IMEI unit baru saat penerimaan barang dari supplier.
     *
     * @param int $storeId
     * @param int $productId
     * @param int|null $variationId
     * @param array $serialNumbers
     * @param int $warrantyMonths
     * @return array
     */
    public function registerSerialNumbers(int $storeId, int $productId, ?int $variationId, array $serialNumbers, int $warrantyMonths = 12): array
    {
        if (!Schema::hasTable('product_serial_numbers')) {
            return ['status' => false, 'message' => 'Tabel product_serial_numbers belum aktif.'];
        }

        $inserted = 0;
        $warrantyExpires = now()->addMonths($warrantyMonths)->format('Y-m-d');

        foreach ($serialNumbers as $sn) {
            $cleanSn = trim($sn);
            if (empty($cleanSn)) {
                continue;
            }

            $exists = DB::table('product_serial_numbers')->where('serial_number', $cleanSn)->exists();
            if ($exists) {
                continue;
            }

            DB::table('product_serial_numbers')->insert([
                'store_id'            => $storeId,
                'product_id'          => $productId,
                'variation_id'        => $variationId,
                'serial_number'       => $cleanSn,
                'status'              => 'in_stock',
                'warranty_expires_at' => $warrantyExpires,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            $inserted++;
        }

        return [
            'status'         => true,
            'inserted_count' => $inserted,
            'message'        => "Berhasil mendaftarkan {$inserted} Serial Number/IMEI unit."
        ];
    }

    /**
     * Mencari status dan informasi garansi berdasarkan Serial Number / IMEI.
     *
     * @param string $serialNumber
     * @return array
     */
    public function lookupSerial(string $serialNumber): array
    {
        if (!Schema::hasTable('product_serial_numbers')) {
            return ['status' => false, 'message' => 'Tabel belum tersedia.'];
        }

        $sn = DB::table('product_serial_numbers')
            ->join('products', 'products.id', '=', 'product_serial_numbers.product_id')
            ->where('product_serial_numbers.serial_number', trim($serialNumber))
            ->select(
                'product_serial_numbers.*',
                'products.name as product_name',
                'products.sku as product_sku'
            )->first();

        if (!$sn) {
            return ['status' => false, 'message' => 'Nomor Seri / IMEI tidak ditemukan.'];
        }

        $isWarrantyValid = $sn->warranty_expires_at ? (now()->format('Y-m-d') <= $sn->warranty_expires_at) : false;

        return [
            'status'           => true,
            'serial_number'    => $sn->serial_number,
            'product_name'     => $sn->product_name,
            'item_status'      => $sn->status,
            'warranty_expires' => $sn->warranty_expires_at,
            'is_warranty_valid'=> $isWarrantyValid,
        ];
    }

    /**
     * Menandai Serial Number sebagai terjual dan menautkannya ke transaksi.
     *
     * @param string $serialNumber
     * @param int $transactionId
     * @return bool
     */
    public function markAsSold(string $serialNumber, int $transactionId): bool
    {
        if (!Schema::hasTable('product_serial_numbers')) {
            return false;
        }

        return DB::table('product_serial_numbers')
            ->where('serial_number', trim($serialNumber))
            ->update([
                'status'         => 'sold',
                'transaction_id' => $transactionId,
                'updated_at'     => now(),
            ]) > 0;
    }
}
