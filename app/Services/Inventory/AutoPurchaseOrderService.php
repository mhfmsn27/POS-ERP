<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Product\ProductVariation;
use App\Models\Master\Supplier;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoPurchaseOrderService
{
    /**
     * Menghasilkan draf Purchase Order (PO) otomatis dari daftar produk yang stoknya menipis.
     *
     * @param int|null $storeId
     * @param int|null $userId
     * @return array
     */
    public function generateDraftPoFromStockAlert(?int $storeId = null, ?int $userId = null): array
    {
        $targetStoreId = $storeId ?: (my_store() ?: 1);
        $creatorId = $userId ?: (auth()->id() ?: 1);

        try {
            // Ambil semua variasi produk yang stoknya di bawah batas alert_quantity
            $lowStockItems = ProductVariation::with(['product', 'stocks' => function ($q) use ($targetStoreId) {
                $q->where('store_id', $targetStoreId);
            }])
            ->whereHas('product', function ($q) use ($targetStoreId) {
                $q->where('store_id', $targetStoreId)
                  ->where('product_type', 'single');
            })
            ->get()
            ->filter(function ($var) use ($targetStoreId) {
                $currentStock = (float)$var->stocks->sum('qty');
                $alertQty = (float)($var->product->alert_quantity ?? 5);
                return $currentStock <= $alertQty;
            });

            if ($lowStockItems->isEmpty()) {
                return [
                    'status'  => false,
                    'message' => 'Semua stok produk saat ini aman (tidak ada barang di bawah stok minimum).'
                ];
            }

            // Ambil atau buat supplier default jika belum ada
            $defaultSupplier = Supplier::withoutGlobalScopes()
                ->where('store_id', $targetStoreId)
                ->first();

            if (!$defaultSupplier) {
                $defaultSupplier = Supplier::withoutGlobalScopes()->create([
                    'store_id' => $targetStoreId,
                    'name'     => 'Supplier Utama POSHUB',
                    'phone'    => '081234567890',
                    'address'  => 'Pusat Logistik Supplier',
                ]);
            }

            return DB::transaction(function () use ($lowStockItems, $targetStoreId, $creatorId, $defaultSupplier) {
                $timestamp = date('Ymd-His');
                $refNo = 'PO-AUTO-' . $timestamp;

                $totalAmount = 0;
                $lineItems = [];

                foreach ($lowStockItems as $var) {
                    $currentStock = (float)$var->stocks->sum('qty');
                    $alertQty = (float)($var->product->alert_quantity ?? 5);
                    // Hitung kuantitas pesan: kembalikan ke level target (2x alert_qty)
                    $orderQty = max(1, (int)ceil(($alertQty * 2) - $currentStock));
                    $unitCost = (float)($var->default_purchase_price ?: ($var->product->purchase_price ?: 0));
                    $subtotal = $orderQty * $unitCost;

                    $totalAmount += $subtotal;
                    $lineItems[] = [
                        'variation_id' => $var->id,
                        'product_id'   => $var->product_id,
                        'qty'          => $orderQty,
                        'unit_cost'    => $unitCost,
                        'subtotal'     => $subtotal,
                        'item_name'    => $var->product->name ?? 'Produk',
                    ];
                }

                // 1. Buat Transaksi PO Utama (Status Draft)
                $transaction = Transaction::withoutGlobalScopes()->create([
                    'store_id'         => $targetStoreId,
                    'supplier_id'      => $defaultSupplier->id,
                    'created_by'       => $creatorId,
                    'type'             => 'purchase',
                    'status'           => 'draft',
                    'payment_status'   => 'due',
                    'ref_no'           => $refNo,
                    'transaction_date' => now(),
                    'total_before_tax' => $totalAmount,
                    'tax_amount'       => 0,
                    'discount_amount'  => 0,
                    'final_total'      => $totalAmount,
                    'notes'            => 'Dibuat otomatis oleh Sistem Auto-Replenishment POSHUB dari Stock Alert',
                ]);

                // 2. Buat Baris Detail Pembelian
                foreach ($lineItems as $line) {
                    Purchase::withoutGlobalScopes()->create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $line['product_id'],
                        'variation_id'   => $line['variation_id'],
                        'quantity'       => $line['qty'],
                        'purchase_price' => $line['unit_cost'],
                    ]);
                }

                Log::info("Auto-PO generated: {$refNo} with " . count($lineItems) . " items for Store #{$targetStoreId}");

                return [
                    'status'         => true,
                    'transaction_id' => $transaction->id,
                    'ref_no'         => $refNo,
                    'supplier_name'  => $defaultSupplier->name,
                    'total_items'    => count($lineItems),
                    'total_amount'   => $totalAmount,
                    'total_formatted'=> 'Rp ' . number_format($totalAmount, 0, ',', '.'),
                    'message'        => "Draf Pesanan Pembelian {$refNo} (" . count($lineItems) . " item) berhasil dibuat otomatis!"
                ];
            });
        } catch (\Throwable $e) {
            Log::error("Failed to generate auto-PO: " . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Gagal membuat draf PO otomatis: ' . $e->getMessage()
            ];
        }
    }
}
