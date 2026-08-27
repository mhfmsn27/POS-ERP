<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StoreCreditGiftCardService
{
    /**
     * Menerbitkan Digital Gift Card / Saldo Prabayar Baru.
     *
     * @param int $storeId
     * @param float $initialBalance
     * @param string $pin 4-6 digit PIN keamanan
     * @param int $validMonths
     * @return array
     */
    public function issueGiftCard(int $storeId, float $initialBalance, string $pin, int $validMonths = 12): array
    {
        if (!Schema::hasTable('digital_gift_cards')) {
            return ['status' => false, 'message' => 'Tabel digital_gift_cards belum aktif.'];
        }

        $cardCode = 'GC-' . strtoupper(bin2hex(random_bytes(4))) . '-' . rand(1000, 9999);
        $expiresAt = now()->addMonths($validMonths)->format('Y-m-d');

        $id = DB::table('digital_gift_cards')->insertGetId([
            'store_id'        => $storeId,
            'card_code'       => $cardCode,
            'pin_hash'        => Hash::make($pin),
            'initial_balance' => $initialBalance,
            'current_balance' => $initialBalance,
            'expires_at'      => $expiresAt,
            'status'          => 'active',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return [
            'status'          => true,
            'card_id'         => $id,
            'card_code'       => $cardCode,
            'initial_balance' => $initialBalance,
            'expires_at'      => $expiresAt,
            'message'         => "Gift Card {$cardCode} senilai Rp " . number_format($initialBalance) . " berhasil diterbitkan."
        ];
    }

    /**
     * Memeriksa sisa saldo Gift Card dengan verifikasi PIN.
     *
     * @param string $cardCode
     * @param string $pin
     * @return array
     */
    public function checkBalance(string $cardCode, string $pin): array
    {
        if (!Schema::hasTable('digital_gift_cards')) {
            return ['status' => false, 'message' => 'Tabel belum tersedia.'];
        }

        $card = DB::table('digital_gift_cards')->where('card_code', trim($cardCode))->first();
        if (!$card) {
            return ['status' => false, 'message' => 'Kode Gift Card tidak ditemukan.'];
        }

        if (!Hash::check($pin, $card->pin_hash)) {
            return ['status' => false, 'message' => 'PIN Gift Card tidak valid.'];
        }

        if ($card->status !== 'active') {
            return ['status' => false, 'message' => "Gift Card berstatus: {$card->status}."];
        }

        if ($card->expires_at && now()->format('Y-m-d') > $card->expires_at) {
            return ['status' => false, 'message' => 'Masa berlaku Gift Card telah kadaluarsa.'];
        }

        return [
            'status'          => true,
            'card_code'       => $card->card_code,
            'current_balance' => (float)$card->current_balance,
            'expires_at'      => $card->expires_at,
        ];
    }

    /**
     * Menggunakan / Memotong saldo Gift Card untuk pembayaran kasir atau e-commerce.
     *
     * @param string $cardCode
     * @param string $pin
     * @param float $amount
     * @param int|null $transactionId
     * @return array
     */
    public function redeemBalance(string $cardCode, string $pin, float $amount, ?int $transactionId = null): array
    {
        if (!Schema::hasTable('digital_gift_cards') || !Schema::hasTable('gift_card_transactions')) {
            return ['status' => false, 'message' => 'Tabel gift card belum aktif.'];
        }

        return DB::transaction(function () use ($cardCode, $pin, $amount, $transactionId) {
            $card = DB::table('digital_gift_cards')->where('card_code', trim($cardCode))->lockForUpdate()->first();
            if (!$card) {
                return ['status' => false, 'message' => 'Gift Card tidak ditemukan.'];
            }

            if (!Hash::check($pin, $card->pin_hash)) {
                return ['status' => false, 'message' => 'PIN Gift Card salah.'];
            }

            if ($card->status !== 'active') {
                return ['status' => false, 'message' => "Gift Card berstatus {$card->status}."];
            }

            $currentBalance = (float)$card->current_balance;
            if ($currentBalance < $amount) {
                return [
                    'status'          => false,
                    'message'         => "Saldo Gift Card tidak mencukupi (Tersedia: Rp " . number_format($currentBalance) . ", Diminta: Rp " . number_format($amount) . ")."
                ];
            }

            $newBalance = $currentBalance - $amount;
            $newStatus  = ($newBalance <= 0) ? 'used' : 'active';

            DB::table('digital_gift_cards')->where('id', $card->id)->update([
                'current_balance' => $newBalance,
                'status'          => $newStatus,
                'updated_at'      => now(),
            ]);

            DB::table('gift_card_transactions')->insert([
                'card_id'        => $card->id,
                'transaction_id' => $transactionId,
                'type'           => 'redeem',
                'amount'         => $amount,
                'balance_after'  => $newBalance,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            return [
                'status'         => true,
                'redeemed_amount'=> $amount,
                'balance_after'  => $newBalance,
                'message'        => "Pembayaran Gift Card Rp " . number_format($amount) . " berhasil. Sisa saldo: Rp " . number_format($newBalance) . "."
            ];
        });
    }
}
