<?php

namespace App\Services\Inventory;

use App\Models\Product\Stock;
use App\Models\Product\Variation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StockTransferService
{
    /**
     * Membuat draft transfer stok antar cabang/gudang.
     *
     * @param array $data
     * @param int $userId
     * @return array
     */
    public function createTransfer(array $data, int $userId): array
    {
        if (!Schema::hasTable('store_transfers') || !Schema::hasTable('store_transfer_items')) {
            return ['status' => false, 'message' => 'Modul transfer stok belum terpasang pada skema database.'];
        }

        return DB::transaction(function () use ($data, $userId) {
            $refNo = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $transferId = DB::table('store_transfers')->insertGetId([
                'ref_no'              => $refNo,
                'from_store_id'       => $data['from_store_id'],
                'to_store_id'         => $data['to_store_id'],
                'status'              => $data['auto_dispatch'] ?? false ? 'in_transit' : 'draft',
                'total_qty_sent'      => 0,
                'total_qty_received'  => 0,
                'discrepancy_qty'     => 0,
                'discrepancy_notes'   => null,
                'sent_by'             => $userId,
                'sent_at'             => now(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            $totalSent = 0;
            foreach ($data['items'] as $item) {
                $qty = (float)$item['qty'];
                $totalSent += $qty;

                DB::table('store_transfer_items')->insert([
                    'transfer_id'     => $transferId,
                    'product_id'      => $item['product_id'],
                    'variation_id'    => $item['variation_id'],
                    'qty_sent'        => $qty,
                    'qty_received'    => 0,
                    'qty_discrepancy' => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                // Jika langsung dispatch (In-Transit), kurangi stok pengirim
                if ($data['auto_dispatch'] ?? false) {
                    $this->deductStock($data['from_store_id'], $item['variation_id'], $qty);
                }
            }

            DB::table('store_transfers')->where('id', $transferId)->update([
                'total_qty_sent' => $totalSent,
            ]);

            return [
                'status'      => true,
                'transfer_id' => $transferId,
                'ref_no'      => $refNo,
                'total_qty'   => $totalSent,
                'message'     => "Dokumen transfer stok {$refNo} berhasil dibuat."
            ];
        });
    }

    /**
     * Mengirim barang (Dispatch to In-Transit). Stok cabang asal dikurangi.
     *
     * @param int $transferId
     * @param int $userId
     * @return array
     */
    public function dispatchTransfer(int $transferId, int $userId): array
    {
        if (!Schema::hasTable('store_transfers')) {
            return ['status' => false, 'message' => 'Modul transfer stok belum terpasang pada skema database.'];
        }

        return DB::transaction(function () use ($transferId, $userId) {
            $transfer = DB::table('store_transfers')->where('id', $transferId)->first();
            if (!$transfer || !in_array($transfer->status, ['draft', 'pending'])) {
                return ['status' => false, 'message' => 'Status transfer tidak valid untuk diberangkatkan.'];
            }

            $items = DB::table('store_transfer_items')->where('transfer_id', $transferId)->get();
            foreach ($items as $item) {
                $this->deductStock($transfer->from_store_id, $item->variation_id, (float)$item->qty_sent);
            }

            DB::table('store_transfers')->where('id', $transferId)->update([
                'status'     => 'in_transit',
                'sent_by'    => $userId,
                'sent_at'    => now(),
                'updated_at' => now(),
            ]);

            return [
                'status'  => true,
                'message' => "Transfer {$transfer->ref_no} berhasil dikirim dan berstatus Dalam Perjalanan (In-Transit)."
            ];
        });
    }

    /**
     * Menerima barang di cabang tujuan dan verifikasi selisih fisik (Discrepancy / Shrinkage).
     *
     * @param int $transferId
     * @param array $receivedItems [variation_id => qty_received]
     * @param string|null $notes
     * @param int $userId
     * @return array
     */
    public function receiveTransfer(int $transferId, array $receivedItems, ?string $notes = null, ?int $userId = null): array
    {
        if (!Schema::hasTable('store_transfers')) {
            return ['status' => false, 'message' => 'Modul transfer stok belum terpasang pada skema database.'];
        }

        return DB::transaction(function () use ($transferId, $receivedItems, $notes, $userId) {
            $transfer = DB::table('store_transfers')->where('id', $transferId)->first();
            if (!$transfer || $transfer->status !== 'in_transit') {
                return ['status' => false, 'message' => 'Hanya transfer berstatus In-Transit yang dapat diterima.'];
            }

            $items = DB::table('store_transfer_items')->where('transfer_id', $transferId)->get();
            $totalReceived = 0;
            $totalDiscrepancy = 0;

            foreach ($items as $item) {
                $receivedQty = isset($receivedItems[$item->variation_id])
                    ? (float)$receivedItems[$item->variation_id]
                    : (float)$item->qty_sent; // Default: terima penuh jika tidak dispesifikasikan

                $discrepancy = (float)$item->qty_sent - $receivedQty;
                $totalReceived += $receivedQty;
                $totalDiscrepancy += $discrepancy;

                DB::table('store_transfer_items')->where('id', $item->id)->update([
                    'qty_received'    => $receivedQty,
                    'qty_discrepancy' => $discrepancy,
                    'updated_at'      => now(),
                ]);

                // Tambahkan stok ke toko tujuan sesuai kuantitas yang benar-benar diterima
                if ($receivedQty > 0) {
                    $this->addStock($transfer->to_store_id, $item->variation_id, $receivedQty);
                }
            }

            DB::table('store_transfers')->where('id', $transferId)->update([
                'status'              => 'received',
                'total_qty_received'  => $totalReceived,
                'discrepancy_qty'     => $totalDiscrepancy,
                'discrepancy_notes'   => $notes,
                'received_by'         => $userId ?? auth()->id() ?? 1,
                'received_at'         => now(),
                'updated_at'          => now(),
            ]);

            Log::info("Transfer {$transfer->ref_no} received. Sent: {$transfer->total_qty_sent}, Received: {$totalReceived}, Discrepancy: {$totalDiscrepancy}");

            return [
                'status'            => true,
                'transfer_id'       => $transferId,
                'total_sent'        => (float)$transfer->total_qty_sent,
                'total_received'    => $totalReceived,
                'total_discrepancy' => $totalDiscrepancy,
                'message'           => "Transfer stok {$transfer->ref_no} berhasil diterima di cabang tujuan."
            ];
        });
    }

    /**
     * Membatalkan transfer in-transit dan mengembalikan stok ke cabang pengirim.
     *
     * @param int $transferId
     * @param int $userId
     * @return array
     */
    public function cancelTransfer(int $transferId, int $userId): array
    {
        if (!Schema::hasTable('store_transfers')) {
            return ['status' => false, 'message' => 'Modul transfer stok belum terpasang pada skema database.'];
        }

        return DB::transaction(function () use ($transferId, $userId) {
            $transfer = DB::table('store_transfers')->where('id', $transferId)->first();
            if (!$transfer || $transfer->status === 'received') {
                return ['status' => false, 'message' => 'Transfer yang sudah diterima tidak dapat dibatalkan.'];
            }

            // Jika status In-Transit, kembalikan stok ke pengirim
            if ($transfer->status === 'in_transit') {
                $items = DB::table('store_transfer_items')->where('transfer_id', $transferId)->get();
                foreach ($items as $item) {
                    $this->addStock($transfer->from_store_id, $item->variation_id, (float)$item->qty_sent);
                }
            }

            DB::table('store_transfers')->where('id', $transferId)->update([
                'status'     => 'cancelled',
                'updated_at' => now(),
            ]);

            return ['status' => true, 'message' => "Transfer {$transfer->ref_no} berhasil dibatalkan."];
        });
    }

    /**
     * Mengurangi kuantitas stok dengan pessimistic lock.
     */
    protected function deductStock(int $storeId, int $variationId, float $qty): void
    {
        $stock = Stock::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('variation_id', $variationId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->qty = (float)$stock->qty - $qty;
            $stock->save();
        }
    }

    /**
     * Menambahkan kuantitas stok.
     */
    protected function addStock(int $storeId, int $variationId, float $qty): void
    {
        $stock = Stock::withoutGlobalScopes()
            ->where('store_id', $storeId)
            ->where('variation_id', $variationId)
            ->lockForUpdate()
            ->first();

        if ($stock) {
            $stock->qty = (float)$stock->qty + $qty;
            $stock->save();
        } else {
            $variation = Variation::withoutGlobalScopes()->find($variationId);
            Stock::create([
                'store_id'     => $storeId,
                'variation_id' => $variationId,
                'product_id'   => $variation ? $variation->product_id : 0,
                'qty'          => $qty,
            ]);
        }
    }
}
