<?php

namespace App\Services\Crm;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use App\Models\Crm\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CustomerRetentionService
{
    /**
     * Memindai pelanggan yang berulang tahun hari ini dan mengirimkan ucapan selamat
     * serta voucher diskon personal melalui WhatsApp CRMHUB OMNICHANNEL.
     *
     * @param int|null $storeId
     * @param float $voucherDiscountPercent
     * @return array
     */
    public function processBirthdayGreetings(?int $storeId = null, float $voucherDiscountPercent = 15): array
    {
        if (!Schema::hasTable('customer_retention_campaigns')) {
            return ['status' => false, 'message' => 'Tabel retensi pelanggan belum aktif.'];
        }

        $todayMonth = date('m');
        $todayDay   = date('d');

        // Cari pelanggan yang tanggal lahirnya cocok dengan hari ini
        $query = Customer::withoutGlobalScopes()
            ->whereNotNull('dob')
            ->whereNotNull('phone')
            ->whereRaw("MONTH(dob) = ? AND DAY(dob) = ?", [$todayMonth, $todayDay]);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $customers = $query->get();
        $sentCount = 0;

        $thisYear = date('Y');

        foreach ($customers as $cust) {
            $phone = trim($cust->phone);
            if (empty($phone)) {
                continue;
            }

            // Cek apakah sudah dikirimi ucapan ultah tahun ini (Idempotency)
            $alreadySent = DB::table('customer_retention_campaigns')
                ->where('customer_id', $cust->id)
                ->where('type', 'birthday')
                ->whereYear('message_sent_at', $thisYear)
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $voucherCode = 'BDAY-' . strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cust->name), 0, 4)) . '-' . rand(100, 999);

            // Catat kampanye ke database
            DB::table('customer_retention_campaigns')->insert([
                'store_id'         => $cust->store_id ?? $storeId ?? 1,
                'type'             => 'birthday',
                'customer_id'      => $cust->id,
                'customer_phone'   => $phone,
                'voucher_code'     => $voucherCode,
                'discount_percent' => $voucherDiscountPercent,
                'message_sent_at'  => now(),
                'is_redeemed'      => false,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $message = "🎉 *Selamat Ulang Tahun, Kak {$cust->name}!* 🎂\n\n"
                . "Seluruh tim kami mendoakan semoga sehat selalu, sukses, dan penuh kebahagiaan.\n\n"
                . "Sebagai hadiah spesial untuk Anda, nikmati *VOUCHER DISKON {$voucherDiscountPercent}%*:\n"
                . "🎁 Kode Kupon: *{$voucherCode}*\n\n"
                . "Tunjukkan pesan ini di kasir kami atau gunakan saat checkout di website.\n\n"
                . "Semoga hari Anda menyenangkan!";

            try {
                SendWhatsappDigitalReceiptJob::dispatch($phone, $message);
                $sentCount++;
            } catch (\Throwable $e) {
                Log::warning("Birthday WA greeting error: " . $e->getMessage());
            }
        }

        return [
            'status'     => true,
            'sent_count' => $sentCount,
            'message'    => "Berhasil memproses dan mengirimkan {$sentCount} ucapan ulang tahun via WhatsApp."
        ];
    }

    /**
     * Mengirimkan pesan siaran (Broadcast) dengan proteksi jam aktif (Warmer Active Hours Anti-Banned).
     *
     * @param int $storeId
     * @param string $message
     * @param array $customerPhones
     * @param int $startHour
     * @param int $endHour
     * @return array
     */
    public function sendSafeBroadcast(int $storeId, string $message, array $customerPhones, int $startHour = 8, int $endHour = 20): array
    {
        $currentHour = (int) date('H');

        // Validasi jam aktif pengiriman agar nomor WA bisnis tidak dicurigai spam
        if ($currentHour < $startHour || $currentHour >= $endHour) {
            return [
                'status'  => false,
                'message' => "Pengiriman dibatasi di luar jam aktif ({$startHour}:00 - {$endHour}:00 WIB) untuk melindungi reputasi nomor WhatsApp."
            ];
        }

        $dispatched = 0;
        foreach ($customerPhones as $phone) {
            if (empty(trim($phone))) continue;
            SendWhatsappDigitalReceiptJob::dispatch(trim($phone), $message);
            $dispatched++;
        }

        return [
            'status'           => true,
            'dispatched_count' => $dispatched,
            'message'          => "Broadcast berhasil dijadwalkan ke antrean untuk {$dispatched} nomor pelanggan."
        ];
    }
}
