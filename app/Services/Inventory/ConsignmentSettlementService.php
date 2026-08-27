<?php

namespace App\Services\Inventory;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConsignmentSettlementService
{
    /**
     * Mendaftarkan produk sebagai barang konsinyasi titip jual dari supplier.
     *
     * @param int $storeId
     * @param int $productId
     * @param int|null $variationId
     * @param int $supplierId
     * @param string $supplierName
     * @param float $consignorSharePercent
     * @param float $storeMarginPercent
     * @param float $consignorCost
     * @return array
     */
    public function registerConsignmentProduct(
        int $storeId,
        int $productId,
        ?int $variationId,
        int $supplierId,
        string $supplierName,
        float $consignorSharePercent = 80.00,
        float $storeMarginPercent = 20.00,
        float $consignorCost = 0
    ): array {
        if (!Schema::hasTable('consignment_products')) {
            return ['status' => false, 'message' => 'Tabel consignment_products belum aktif.'];
        }

        DB::table('consignment_products')->updateOrInsert(
            [
                'store_id'     => $storeId,
                'product_id'   => $productId,
                'supplier_id'  => $supplierId,
            ],
            [
                'variation_id'            => $variationId,
                'supplier_name'           => $supplierName,
                'consignor_share_percent' => $consignorSharePercent,
                'store_margin_percent'    => $storeMarginPercent,
                'unit_consignor_cost'     => $consignorCost,
                'is_active'               => true,
                'updated_at'              => now(),
            ]
        );

        return [
            'status'  => true,
            'message' => "Produk konsinyasi dari {$supplierName} (Bagi Hasil: {$consignorSharePercent}%) berhasil disimpan."
        ];
    }

    /**
     * Menghasilkan Laporan Rekapitulasi Pelunasan Konsinyasi (Settlement Statement) per periode.
     *
     * @param int $storeId
     * @param int $supplierId
     * @param string $startDate Format Y-m-d
     * @param string $endDate Format Y-m-d
     * @return array
     */
    public function generateSettlement(int $storeId, int $supplierId, string $startDate, string $endDate): array
    {
        if (!Schema::hasTable('consignment_settlements') || !Schema::hasTable('consignment_products')) {
            return ['status' => false, 'message' => 'Tabel konsinyasi belum aktif.'];
        }

        $consignProducts = DB::table('consignment_products')
            ->where('store_id', $storeId)
            ->where('supplier_id', $supplierId)
            ->where('is_active', true)
            ->get();

        if ($consignProducts->isEmpty()) {
            return ['status' => false, 'message' => 'Tidak ada produk konsinyasi aktif untuk supplier ini.'];
        }

        $productIds = $consignProducts->pluck('product_id')->toArray();

        // Ambil penjualan faktur berstatus final dalam rentang tanggal
        $sales = DB::table('sells')
            ->join('transactions', 'transactions.id', '=', 'sells.transaction_id')
            ->where('transactions.store_id', $storeId)
            ->whereIn('sells.product_id', $productIds)
            ->where('transactions.status', 'final')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->select(
                'sells.product_id',
                DB::raw('sum(sells.qty) as qty_sold'),
                DB::raw('sum(sells.qty * sells.unit_price) as gross_amount')
            )
            ->groupBy('sells.product_id')
            ->get();

        $totalQty = 0;
        $totalGross = 0;
        $totalPayable = 0;
        $totalStoreFee = 0;

        foreach ($sales as $sale) {
            $conf = $consignProducts->where('product_id', $sale->product_id)->first();
            $sharePercent = $conf ? (float)$conf->consignor_share_percent : 80.0;
            $marginPercent = $conf ? (float)$conf->store_margin_percent : 20.0;

            $gross = (float)$sale->gross_amount;
            $payable = ($sharePercent / 100) * $gross;
            $fee = ($marginPercent / 100) * $gross;

            $totalQty += (float)$sale->qty_sold;
            $totalGross += $gross;
            $totalPayable += $payable;
            $totalStoreFee += $fee;
        }

        $settlementNo = 'CSL-' . date('Ymd') . '-' . rand(100, 999);

        $id = DB::table('consignment_settlements')->insertGetId([
            'store_id'                => $storeId,
            'supplier_id'             => $supplierId,
            'settlement_no'           => $settlementNo,
            'start_date'              => $startDate,
            'end_date'                => $endDate,
            'total_qty_sold'          => $totalQty,
            'total_gross_sales'       => $totalGross,
            'total_consignor_payable' => $totalPayable,
            'total_store_fee'         => $totalStoreFee,
            'status'                  => 'draft',
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        return [
            'status'                  => true,
            'settlement_id'           => $id,
            'settlement_no'           => $settlementNo,
            'start_date'              => $startDate,
            'end_date'                => $endDate,
            'total_qty_sold'          => $totalQty,
            'total_gross_sales'       => $totalGross,
            'total_consignor_payable' => $totalPayable,
            'total_store_fee'         => $totalStoreFee,
            'message'                 => "Rekapitulasi Konsinyasi {$settlementNo} berhasil dibuat. Hak Mitra: Rp " . number_format($totalPayable) . " (Margin Toko: Rp " . number_format($totalStoreFee) . ")."
        ];
    }

    /**
     * Mengambil riwayat laporan pelunasan konsinyasi.
     *
     * @param int $storeId
     * @param int|null $supplierId
     * @return array
     */
    public function getSettlementHistory(int $storeId, ?int $supplierId = null): array
    {
        if (!Schema::hasTable('consignment_settlements')) {
            return ['status' => true, 'settlements' => []];
        }

        $query = DB::table('consignment_settlements')
            ->where('store_id', $storeId);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $list = $query->orderBy('created_at', 'desc')->get();

        return [
            'status'            => true,
            'total_settlements' => count($list),
            'settlements'       => $list
        ];
    }

    /**
     * Memperbarui status pelunasan konsinyasi (draft -> approved -> paid).
     *
     * @param int $settlementId
     * @param string $status
     * @return array
     */
    public function updateSettlementStatus(int $settlementId, string $status): array
    {
        if (!Schema::hasTable('consignment_settlements')) {
            return ['status' => false, 'message' => 'Tabel belum aktif.'];
        }

        DB::table('consignment_settlements')->where('id', $settlementId)->update([
            'status'     => $status,
            'updated_at' => now(),
        ]);

        return [
            'status'        => true,
            'settlement_id' => $settlementId,
            'new_status'    => $status,
            'message'       => "Status pelunasan konsinyasi berhasil diubah menjadi {$status}."
        ];
    }
}
