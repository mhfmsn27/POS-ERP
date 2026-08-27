<?php

namespace App\Services\Crm;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CustomerLoyaltyService
{
    // Konfigurasi Default Loyalty Points
    const AMOUNT_PER_POINT = 10000; // Rp 10.000 = 1 Poin
    const VALUE_PER_POINT  = 100;   // 1 Poin = Potongan Rp 100

    /**
     * Menghitung poin yang didapat dari total belanja.
     *
     * @param float $amount
     * @return int
     */
    public function calculateEarnedPoints(float $amount): int
    {
        if ($amount <= 0) {
            return 0;
        }
        return (int) floor($amount / self::AMOUNT_PER_POINT);
    }

    /**
     * Menambahkan poin loyalitas dari transaksi penjualan kasir.
     *
     * @param int $customerId
     * @param int $storeId
     * @param int $transactionId
     * @param float $finalTotal
     * @return int Points earned
     */
    public function addPointsForSale(int $customerId, int $storeId, int $transactionId, float $finalTotal): int
    {
        if ($customerId <= 0 || $finalTotal <= 0) {
            return 0;
        }

        try {
            if (!Schema::hasTable('customer_loyalty_points')) {
                return 0;
            }

            $points = $this->calculateEarnedPoints($finalTotal);
            if ($points <= 0) {
                return 0;
            }

            $currentBalance = $this->getBalance($customerId);
            $newBalance = $currentBalance + $points;

            DB::table('customer_loyalty_points')->insert([
                'customer_id'     => $customerId,
                'store_id'        => $storeId,
                'transaction_id'  => $transactionId,
                'points_earned'   => $points,
                'points_redeemed' => 0,
                'balance_after'   => $newBalance,
                'type'            => 'earn',
                'notes'           => "Poin diperoleh dari transaksi #{$transactionId} (Belanja Rp " . number_format($finalTotal) . ")",
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            Log::info("Customer #{$customerId} earned {$points} loyalty points from Transaction #{$transactionId}. New Balance: {$newBalance}");

            return $points;
        } catch (\Throwable $e) {
            Log::warning("Failed to add loyalty points: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Menukarkan (Redeem) poin loyalitas menjadi diskon potongan harga kasir.
     *
     * @param int $customerId
     * @param int $storeId
     * @param int $pointsToRedeem
     * @param int|null $transactionId
     * @return array [status, discount_amount, new_balance, message]
     */
    public function redeemPoints(int $customerId, int $storeId, int $pointsToRedeem, ?int $transactionId = null): array
    {
        try {
            if (!Schema::hasTable('customer_loyalty_points')) {
                return [
                    'status'          => false,
                    'discount_amount' => 0,
                    'current_balance' => 0,
                    'message'         => 'Modul loyalty points belum aktif pada database.'
                ];
            }

            $currentBalance = $this->getBalance($customerId);
            if ($pointsToRedeem <= 0 || $pointsToRedeem > $currentBalance) {
                return [
                    'status'          => false,
                    'discount_amount' => 0,
                    'current_balance' => $currentBalance,
                    'message'         => "Poin tidak mencukupi. Saldo poin saat ini: {$currentBalance} poin."
                ];
            }

            $discountAmount = $pointsToRedeem * self::VALUE_PER_POINT;
            $newBalance = $currentBalance - $pointsToRedeem;

            DB::table('customer_loyalty_points')->insert([
                'customer_id'     => $customerId,
                'store_id'        => $storeId,
                'transaction_id'  => $transactionId,
                'points_earned'   => 0,
                'points_redeemed' => $pointsToRedeem,
                'balance_after'   => $newBalance,
                'type'            => 'redeem',
                'notes'           => "Penukaran {$pointsToRedeem} poin menjadi potongan belanja Rp " . number_format($discountAmount),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            return [
                'status'          => true,
                'points_redeemed' => $pointsToRedeem,
                'discount_amount' => (float)$discountAmount,
                'new_balance'     => $newBalance,
                'message'         => "Berhasil menukar {$pointsToRedeem} poin untuk potongan Rp " . number_format($discountAmount) . "."
            ];
        } catch (\Throwable $e) {
            return [
                'status'          => false,
                'discount_amount' => 0,
                'current_balance' => 0,
                'message'         => 'Gagal memproses penukaran poin: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Mendapatkan saldo poin aktif pelanggan saat ini.
     *
     * @param int $customerId
     * @return int
     */
    public function getBalance(int $customerId): int
    {
        try {
            if (!Schema::hasTable('customer_loyalty_points')) {
                return 0;
            }

            $lastLog = DB::table('customer_loyalty_points')
                ->where('customer_id', $customerId)
                ->orderBy('id', 'desc')
                ->first();

            return $lastLog ? (int)$lastLog->balance_after : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Mendapatkan tingkatan VIP Membership (Bronze, Silver, Gold, Platinum) berdasarkan akumulasi poin.
     *
     * @param int $customerId
     * @return array
     */
    public function getCustomerTier(int $customerId): array
    {
        try {
            if (!Schema::hasTable('customer_loyalty_points')) {
                return [
                    'tier_name'       => 'Bronze Member',
                    'total_earned'    => 0,
                    'current_balance' => 0,
                    'discount_rate'   => 0.00,
                    'discount_label'  => '0% Auto Diskon',
                    'badge_color'     => '#b45309',
                ];
            }

            $totalEarned = (int) DB::table('customer_loyalty_points')
                ->where('customer_id', $customerId)
                ->sum('points_earned');

            $currentBalance = $this->getBalance($customerId);

            if ($totalEarned >= 5000) {
                $tier = 'Platinum VIP';
                $discountRate = 0.10; // 10%
                $color = '#8b5cf6'; // Purple
            } elseif ($totalEarned >= 2000) {
                $tier = 'Gold VIP';
                $discountRate = 0.05; // 5%
                $color = '#f59e0b'; // Amber Gold
            } elseif ($totalEarned >= 500) {
                $tier = 'Silver Member';
                $discountRate = 0.02; // 2%
                $color = '#64748b'; // Slate Silver
            } else {
                $tier = 'Bronze Member';
                $discountRate = 0.00;
                $color = '#b45309'; // Bronze
            }

            return [
                'tier_name'       => $tier,
                'total_earned'    => $totalEarned,
                'current_balance' => $currentBalance,
                'discount_rate'   => $discountRate,
                'discount_label'  => ($discountRate * 100) . '% Auto Diskon',
                'badge_color'     => $color,
            ];
        } catch (\Throwable $e) {
            return [
                'tier_name'       => 'Bronze Member',
                'total_earned'    => 0,
                'current_balance' => 0,
                'discount_rate'   => 0.00,
                'discount_label'  => '0% Auto Diskon',
                'badge_color'     => '#b45309',
            ];
        }
    }
}
