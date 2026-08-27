<?php

namespace App\Services\Manufacturing;

use App\Models\Product\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ManufacturingService
{
    /**
     * Mendaftarkan Formula Resep / Bill of Materials (BOM) baru.
     *
     * @param int $storeId
     * @param int $finishedProductId
     * @param int|null $finishedVariationId
     * @param string $name
     * @param float $yieldQty
     * @param array $rawMaterials Array of ['raw_product_id', 'raw_variation_id', 'quantity', 'unit_cost']
     * @param string|null $notes
     * @return array
     */
    public function createBom(
        int $storeId,
        int $finishedProductId,
        ?int $finishedVariationId,
        string $name,
        float $yieldQty,
        array $rawMaterials,
        ?string $notes = null
    ): array {
        if (!Schema::hasTable('bill_of_materials') || !Schema::hasTable('bill_of_materials_items')) {
            return ['status' => false, 'message' => 'Tabel BOM belum aktif.'];
        }

        return DB::transaction(function () use ($storeId, $finishedProductId, $finishedVariationId, $name, $yieldQty, $rawMaterials, $notes) {
            $bomId = DB::table('bill_of_materials')->insertGetId([
                'store_id'              => $storeId,
                'finished_product_id'   => $finishedProductId,
                'finished_variation_id' => $finishedVariationId,
                'name'                  => $name,
                'yield_quantity'        => $yieldQty > 0 ? $yieldQty : 1,
                'notes'                 => $notes,
                'is_active'             => true,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            $insertedItems = 0;
            foreach ($rawMaterials as $raw) {
                $rawProdId = (int)($raw['raw_product_id'] ?? 0);
                $rawVarId  = isset($raw['raw_variation_id']) ? (int)$raw['raw_variation_id'] : null;
                $rawQty    = (float)($raw['quantity'] ?? 1);
                $unitCost  = (float)($raw['unit_cost'] ?? 0);

                if ($rawProdId > 0 && $rawQty > 0) {
                    DB::table('bill_of_materials_items')->insert([
                        'bom_id'           => $bomId,
                        'raw_product_id'   => $rawProdId,
                        'raw_variation_id' => $rawVarId,
                        'quantity'         => $rawQty,
                        'unit_cost'        => $unitCost,
                        'created_at'       => now(),
                        'updated_at'       => now(),
                    ]);
                    $insertedItems++;
                }
            }

            return [
                'status'         => true,
                'bom_id'         => $bomId,
                'name'           => $name,
                'total_raw_items'=> $insertedItems,
                'message'        => "Resep BOM '{$name}' berhasil dibuat dengan {$insertedItems} bahan baku."
            ];
        });
    }

    /**
     * Membuat Surat Perintah Kerja (SPK / Work Order) Produksi.
     *
     * @param int $storeId
     * @param int $bomId
     * @param float $targetQty
     * @return array
     */
    public function createWorkOrder(int $storeId, int $bomId, float $targetQty): array
    {
        if (!Schema::hasTable('manufacturing_work_orders')) {
            return ['status' => false, 'message' => 'Tabel work orders belum tersedia.'];
        }

        $orderNo = 'WO-' . date('Ymd') . '-' . rand(100, 999);

        $id = DB::table('manufacturing_work_orders')->insertGetId([
            'store_id'        => $storeId,
            'bom_id'          => $bomId,
            'order_no'        => $orderNo,
            'target_quantity' => $targetQty,
            'actual_quantity' => 0,
            'status'          => 'draft',
            'total_cost'      => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return [
            'status'        => true,
            'work_order_id' => $id,
            'order_no'      => $orderNo,
            'message'       => "Surat Perintah Produksi {$orderNo} berhasil dibuat."
        ];
    }

    /**
     * Mengeksekusi proses produksi: Otomatis memotong stok bahan baku dan menambah stok barang jadi.
     *
     * @param int $workOrderId
     * @param float|null $actualProducedQty
     * @return array
     * @throws \Exception
     */
    public function executeWorkOrder(int $workOrderId, ?float $actualProducedQty = null): array
    {
        if (!Schema::hasTable('manufacturing_work_orders')) {
            return ['status' => false, 'message' => 'Tabel work order belum aktif.'];
        }

        return DB::transaction(function () use ($workOrderId, $actualProducedQty) {
            $wo = DB::table('manufacturing_work_orders')->where('id', $workOrderId)->lockForUpdate()->first();
            if (!$wo) {
                throw new \Exception("Work Order #{$workOrderId} tidak ditemukan.");
            }

            if ($wo->status === 'completed') {
                return ['status' => true, 'message' => 'Work Order sudah selesai sebelumnya.'];
            }

            $bom = DB::table('bill_of_materials')->where('id', $wo->bom_id)->first();
            if (!$bom) {
                throw new \Exception("Formula BOM untuk Work Order #{$workOrderId} tidak ditemukan.");
            }

            $producedQty = $actualProducedQty ?? (float)$wo->target_quantity;
            $multiplier  = $producedQty / max(1, (float)$bom->yield_quantity);

            $rawItems = DB::table('bill_of_materials_items')->where('bom_id', $bom->id)->get();
            $totalProductionCost = 0;

            // 1. Potong Bahan Baku Mentah dengan Lock
            foreach ($rawItems as $raw) {
                $requiredQty = (float)$raw->quantity * $multiplier;
                $stockQuery = Stock::withoutGlobalScopes()
                    ->where('store_id', $wo->store_id)
                    ->where('product_id', $raw->raw_product_id);

                if (!empty($raw->raw_variation_id)) {
                    $stockQuery->where('variation_id', $raw->raw_variation_id);
                }

                $stock = $stockQuery->lockForUpdate()->first();

                if (!$stock || (float)$stock->qty_available < $requiredQty) {
                    $avail = $stock ? (float)$stock->qty_available : 0;
                    throw new \Exception("Bahan baku ID {$raw->raw_product_id} tidak mencukupi. Butuh: {$requiredQty}, Tersedia: {$avail}");
                }

                $stock->qty_available = (float)$stock->qty_available - $requiredQty;
                $stock->save();

                $totalProductionCost += ($requiredQty * (float)$raw->unit_cost);
            }

            // 2. Tambah Stok Barang Jadi
            $finishedStockQuery = Stock::withoutGlobalScopes()
                ->where('store_id', $wo->store_id)
                ->where('product_id', $bom->finished_product_id);

            if (!empty($bom->finished_variation_id)) {
                $finishedStockQuery->where('variation_id', $bom->finished_variation_id);
            }

            $finishedStock = $finishedStockQuery->lockForUpdate()->first();

            if ($finishedStock) {
                $finishedStock->qty_available = (float)$finishedStock->qty_available + $producedQty;
                $finishedStock->save();
            } else {
                Stock::create([
                    'store_id'      => $wo->store_id,
                    'product_id'    => $bom->finished_product_id,
                    'variation_id'  => $bom->finished_variation_id,
                    'qty_available' => $producedQty,
                ]);
            }

            // 3. Update Status Work Order
            DB::table('manufacturing_work_orders')->where('id', $wo->id)->update([
                'status'          => 'completed',
                'actual_quantity' => $producedQty,
                'total_cost'      => $totalProductionCost,
                'completed_at'    => now(),
                'updated_at'      => now(),
            ]);

            $unitHpp = $producedQty > 0 ? round($totalProductionCost / $producedQty, 2) : 0;

            return [
                'status'           => true,
                'order_no'         => $wo->order_no,
                'actual_quantity'  => $producedQty,
                'total_cost'       => $totalProductionCost,
                'unit_hpp'         => $unitHpp,
                'message'          => "Produksi selesai: {$producedQty} unit '{$bom->name}' berhasil dibuat (HPP Satuan: Rp " . number_format($unitHpp) . ")."
            ];
        });
    }
}
