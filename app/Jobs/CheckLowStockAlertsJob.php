<?php

namespace App\Jobs;

use App\Models\Admin\NotificationSetting;
use App\Models\Admin\Store;
use App\Models\Product\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckLowStockAlertsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Dapatkan seluruh stok yang berada pada atau di bawah batas alert_quantity
            $lowStockItems = Stock::withoutGlobalScopes()
                ->with(['product:id,name,alert_quantity,is_stock', 'variation:id,name', 'store:id,name,phone'])
                ->whereHas('product', function ($q) {
                    $q->where('is_stock', 'yes')->where('alert_quantity', '>', 0);
                })
                ->whereRaw('stocks.qty_available <= (SELECT alert_quantity FROM products WHERE products.id = stocks.product_id)')
                ->get();

            if ($lowStockItems->isEmpty()) {
                Log::info('Low Stock Alert Check: Tidak ada produk yang menipis.');
                return;
            }

            // Kelompokkan berdasarkan cabang toko (store_id)
            $groupedByStore = $lowStockItems->groupBy('store_id');

            foreach ($groupedByStore as $storeId => $items) {
                $store = Store::withoutGlobalScopes()->find($storeId);
                $storeName = $store->name ?? 'Cabang Toko';
                $itemCount = $items->count();

                $messageLines = [];
                $messageLines[] = "⚠️ *PERINGATAN STOK MENIPIS - {$storeName}*";
                $messageLines[] = "Terdapat {$itemCount} item produk yang telah mencapai batas minimum:\n";

                $counter = 1;
                foreach ($items->take(15) as $stock) {
                    $prodName = $stock->product->name ?? 'Produk';
                    $varName = !empty($stock->variation->name) && $stock->variation->name !== 'default' ? " ({$stock->variation->name})" : "";
                    $qty = $stock->qty_available;
                    $min = $stock->product->alert_quantity ?? 0;
                    $messageLines[] = "{$counter}. {$prodName}{$varName}: *Sisa {$qty}* (Batas: {$min})";
                    $counter++;
                }

                if ($itemCount > 15) {
                    $remaining = $itemCount - 15;
                    $messageLines[] = "...dan {$remaining} produk lainnya.";
                }

                $messageLines[] = "\nSilakan lakukan Purchase Order (PO) atau Transfer Stok.";
                $fullMessage = implode("\n", $messageLines);

                // Kirim notifikasi jika nomor HP toko atau notifikasi tersedia
                $settData = NotificationSetting::withoutGlobalScopes()->where('store_id', $storeId)->first() 
                    ?? NotificationSetting::withoutGlobalScopes()->whereNull('store_id')->first();

                $targetPhone = !empty($store->phone) ? $store->phone : ($settData->phone ?? null);

                if (!empty($targetPhone) && !empty($settData->device_key) && !empty($settData->api_key)) {
                    SendWhatsappNotificationJob::dispatch(
                        $fullMessage,
                        $targetPhone,
                        $settData->device_key,
                        $settData->api_key
                    );
                }

                Log::info("Low stock alert generated for store [{$storeName}] with {$itemCount} items.");
            }
        } catch (\Throwable $e) {
            Log::error("CheckLowStockAlertsJob failed: " . $e->getMessage());
        }
    }
}
