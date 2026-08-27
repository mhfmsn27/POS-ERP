<?php

namespace App\Services\Webhook;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use App\Models\Crm\Customer;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsappInteractiveBotService
{
    /**
     * Memproses pesan masuk dari Webhook CRMHUB OMNICHANNEL dan menghasilkan respon otomatis secara cerdas.
     *
     * @param string $senderPhone
     * @param string $messageText
     * @param int $storeId
     * @return array
     */
    public function handleIncomingMessage(string $senderPhone, string $messageText, int $storeId = 1): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $senderPhone);
        $cleanText  = trim(strtoupper($messageText));

        $replyMessage = "";

        // 1. Perintah CEK RESI / STATUS PESANAN
        if (str_starts_with($cleanText, 'CEK RESI') || str_starts_with($cleanText, 'STATUS')) {
            $parts = explode(' ', $cleanText);
            $queryNo = end($parts);
            $queryNo = trim(str_replace('#', '', $queryNo));

            if ($queryNo === 'RESI' || $queryNo === 'STATUS' || empty($queryNo) || count($parts) < 2) {
                $replyMessage = "Silakan sertakan nomor pesanan / nomor faktur yang ingin dicek.\n\nContoh format: *CEK RESI PSL-001* atau *CEK RESI 10023*";
            } else {
                $trx = Transaction::withoutGlobalScopes()
                    ->where('ref_no', 'like', "%{$queryNo}%")
                    ->orWhere('invoice_no', 'like', "%{$queryNo}%")
                    ->first();

                if ($trx) {
                    $ship = $trx->shipping_detail;
                    $resiNo = $ship->resi_no ?? 'Belum terbit';
                    $courier = ($ship->curir_name ?? 'Kurir') . ' ' . ($ship->curir_service ?? '');
                    $statusMap = [
                        'ordered' => 'Sedang Diproses',
                        'transit' => 'Dalam Pengiriman (On The Way)',
                        'final'   => 'Selesai / Terkirim',
                        'cancel'  => 'Dibatalkan',
                    ];
                    $st = $statusMap[$trx->status] ?? ucfirst($trx->status);

                    $replyMessage = "📦 *STATUS PESANAN #{$trx->ref_no}*\n\n"
                        . "📌 Status: *{$st}*\n"
                        . "🚚 Ekspedisi: *{$courier}*\n"
                        . "📋 No. Resi: *{$resiNo}*\n"
                        . "💰 Total: Rp " . number_format($trx->final_total) . "\n\n"
                        . "Terima kasih telah berbelanja di toko kami!";
                } else {
                    $replyMessage = "Mohon maaf, nomor pesanan *{$queryNo}* tidak ditemukan dalam sistem kami. Pastikan format: *CEK RESI [No_Pesanan]* (contoh: *CEK RESI PSL-001*).";
                }
            }
        }
        // 2. Perintah CEK POIN / INFO POIN
        elseif (str_contains($cleanText, 'POIN') || str_contains($cleanText, 'LOYALTI')) {
            $customer = Customer::withoutGlobalScopes()
                ->where('phone', 'like', "%" . substr($cleanPhone, -9) . "%")
                ->first();

            if ($customer) {
                $points = 0;
                if (\Illuminate\Support\Facades\Schema::hasTable('customer_loyalty_points')) {
                    $lastLog = DB::table('customer_loyalty_points')
                        ->where('customer_id', $customer->id)
                        ->orderBy('id', 'desc')
                        ->first();
                    $points = $lastLog ? (int)$lastLog->balance_after : 0;
                }

                $replyMessage = "👑 *INFORMASI MEMBER & POIN LOYALITAS*\n\n"
                    . "👤 Nama: *{$customer->name}*\n"
                    . "⭐ Saldo Poin: *{$points} Poin*\n"
                    . "💎 Tier Member: *" . ucfirst($customer->vip_tier ?? 'Bronze') . "*\n\n"
                    . "Tukarkan poin Anda di kasir atau saat checkout website kami untuk mendapatkan potongan harga spesial!";
            } else {
                $replyMessage = "Nomor WhatsApp Anda belum terdaftar sebagai Member. Kunjungi kasir kami atau daftar melalui website untuk mulai mengumpulkan poin!";
            }
        }
        // 3. Perintah PROMO / FLASH SALE
        elseif (str_contains($cleanText, 'PROMO') || str_contains($cleanText, 'FLASH') || str_contains($cleanText, 'DISKON')) {
            $replyMessage = "🔥 *PROMO SPESIAL HARI INI* 🔥\n\n"
                . "Nikmati diskon s/d 50% untuk produk pilihan dan GRATIS ONGKIR dengan Ambil di Toko (BOPIS)!\n\n"
                . "Kunjungi katalog lengkap di website resmi kami atau balas pesan ini jika membutuhkan bantuan.";
        }
        // 4. Default MENU / BANTUAN
        else {
            $replyMessage = "Halo! Selamat datang di Layanan Pelanggan Otomatis *POSHUB* 🤖\n\n"
                . "Ketik perintah berikut untuk info cepat:\n"
                . "1️⃣ *CEK RESI [No_Faktur]* (contoh: CEK RESI PSL-001)\n"
                . "2️⃣ *INFO POIN* (Cek saldo poin & tier member Anda)\n"
                . "3️⃣ *PROMO* (Dapatkan informasi promo & diskon terbaru)\n\n"
                . "Tim CS kami siap membantu Anda.";
        }

        // Kirim balasan otomatis via CRMHUB OMNICHANNEL WA Gateway
        try {
            SendWhatsappDigitalReceiptJob::dispatch($cleanPhone, $replyMessage);
        } catch (\Throwable $e) {
            Log::warning("Bot auto-reply error: " . $e->getMessage());
        }

        return [
            'status'        => true,
            'sender_phone'  => $cleanPhone,
            'reply_message' => $replyMessage
        ];
    }
}
