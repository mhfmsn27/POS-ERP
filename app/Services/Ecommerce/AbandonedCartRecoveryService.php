<?php

namespace App\Services\Ecommerce;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AbandonedCartRecoveryService
{
    /**
     * Mencatat atau memperbarui keranjang belanja aktif pelanggan.
     *
     * @param int $storeId
     * @param array $cartItems
     * @param int|null $customerId
     * @param string|null $customerPhone
     * @param string|null $customerName
     * @return int ID log keranjang
     */
    public function trackCart(int $storeId, array $cartItems, ?int $customerId = null, ?string $customerPhone = null, ?string $customerName = null): int
    {
        if (!Schema::hasTable('abandoned_cart_logs') || empty($cartItems)) {
            return 0;
        }

        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $price = (float)($item['price'] ?? ($item['selling_price'] ?? 0));
            $qty   = (int)($item['quantity'] ?? ($item['qty'] ?? 1));
            $totalAmount += ($price * $qty);
        }

        // Cek jika sudah ada log pending untuk customer ini
        $existing = null;
        if ($customerId) {
            $existing = DB::table('abandoned_cart_logs')
                ->where('store_id', $storeId)
                ->where('customer_id', $customerId)
                ->where('status', 'pending')
                ->first();
        }

        if ($existing) {
            DB::table('abandoned_cart_logs')->where('id', $existing->id)->update([
                'cart_payload'   => json_encode($cartItems),
                'total_amount'   => $totalAmount,
                'customer_phone' => $customerPhone ?? $existing->customer_phone,
                'customer_name'  => $customerName ?? $existing->customer_name,
                'updated_at'     => now(),
            ]);
            return $existing->id;
        }

        return DB::table('abandoned_cart_logs')->insertGetId([
            'store_id'       => $storeId,
            'customer_id'    => $customerId,
            'customer_phone' => $customerPhone,
            'customer_name'  => $customerName ?? 'Pelanggan Setia',
            'cart_payload'   => json_encode($cartItems),
            'total_amount'   => $totalAmount,
            'status'         => 'pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    /**
     * Memindai keranjang yang ditinggalkan lebih dari batas waktu (default 2 jam)
     * dan mengirimkan pesan notifikasi pengingat via WhatsApp CRMHUB OMNICHANNEL.
     *
     * @param int $thresholdHours
     * @return array
     */
    public function processAbandonedCarts(int $thresholdHours = 2): array
    {
        if (!Schema::hasTable('abandoned_cart_logs')) {
            return ['status' => false, 'message' => 'Tabel abandoned cart belum aktif.'];
        }

        $cutoffTime = now()->subHours($thresholdHours);

        $abandoned = DB::table('abandoned_cart_logs')
            ->where('status', 'pending')
            ->whereNotNull('customer_phone')
            ->where('updated_at', '<=', $cutoffTime)
            ->limit(50)
            ->get();

        $sentCount = 0;

        foreach ($abandoned as $cart) {
            $phone = trim($cart->customer_phone);
            if (empty($phone)) {
                continue;
            }

            // Normalisasi nomor WA
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            if (substr($cleanPhone, 0, 1) === '0') {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }

            $items = json_decode($cart->cart_payload, true) ?: [];
            $itemsSummary = '';
            foreach (array_slice($items, 0, 3) as $it) {
                $name = $it['name'] ?? ($it['product_name'] ?? 'Produk Pilihan');
                $qty  = $it['quantity'] ?? ($it['qty'] ?? 1);
                $itemsSummary .= "• {$name} ({$qty}x)\n";
            }

            $waMessage = "Halo Kak *{$cart->customer_name}*,\n\n"
                . "Kami melihat Anda meninggalkan beberapa item favorit di keranjang belanja:\n\n"
                . "{$itemsSummary}\n"
                . "Total Nilai: *Rp " . number_format($cart->total_amount) . "*\n\n"
                . "Stok produk terbatas! Segera selesaikan pesanan Anda sekarang agar tidak kehabisan.\n\n"
                . "Terima kasih!";

            try {
                // Dispatch pengiriman pesan WA via asynchronous queue job
                SendWhatsappDigitalReceiptJob::dispatch($cleanPhone, $waMessage);

                DB::table('abandoned_cart_logs')->where('id', $cart->id)->update([
                    'status'     => 'notified',
                    'wa_sent_at' => now(),
                    'updated_at' => now(),
                ]);

                $sentCount++;
            } catch (\Throwable $e) {
                Log::warning("[ABANDONED CART] Gagal kirim WA ke {$cleanPhone}: " . $e->getMessage());
            }
        }

        return [
            'status'     => true,
            'sent_count' => $sentCount,
            'message'    => "Berhasil memproses {$sentCount} pengingat keranjang belanja via WhatsApp."
        ];
    }
}
