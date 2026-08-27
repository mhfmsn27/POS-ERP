<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PosSecurityAuditService
{
    /**
     * Mencatat aksi pembukaan laci kasir manual tanpa transaksi (No-Sale Drawer Kick).
     *
     * @param int $storeId
     * @param int $userId
     * @param string|null $reason
     * @return void
     */
    public function logDrawerKick(int $storeId, int $userId, ?string $reason = null): void
    {
        $this->logEvent($storeId, $userId, 'drawer_open_no_sale', null, [
            'reason'     => $reason ?? 'Buka laci kasir manual tanpa transaksi (No-Sale)',
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Mencatat penghapusan item dari keranjang setelah di-scan.
     *
     * @param int $storeId
     * @param int $userId
     * @param array $itemData
     * @param string|null $refNo
     * @return void
     */
    public function logCartItemRemoved(int $storeId, int $userId, array $itemData, ?string $refNo = null): void
    {
        $this->logEvent($storeId, $userId, 'cart_item_removed', $refNo, [
            'item_name' => $itemData['name'] ?? 'Unknown Item',
            'qty'       => $itemData['qty'] ?? 1,
            'subtotal'  => $itemData['subtotal'] ?? 0,
        ]);
    }

    /**
     * Mencatat pemberian diskon manual yang melampaui batas kewenangan (Discount Override).
     *
     * @param int $storeId
     * @param int $userId
     * @param float $discountPercent
     * @param float $discountAmount
     * @param string|null $refNo
     * @return void
     */
    public function logDiscountOverride(int $storeId, int $userId, float $discountPercent, float $discountAmount, ?string $refNo = null): void
    {
        $this->logEvent($storeId, $userId, 'discount_override', $refNo, [
            'discount_percent' => $discountPercent,
            'discount_amount'  => $discountAmount,
            'warning'          => 'Diskon melebihi ambang batas wewenang kasir standar (>15%)',
        ]);
    }

    /**
     * Mencatat pembatalan/penghapusan transaksi (Void Transaction).
     *
     * @param int $storeId
     * @param int $userId
     * @param int|string $transactionId
     * @param string $reason
     * @return void
     */
    public function logTransactionVoided(int $storeId, int $userId, $transactionId, string $reason): void
    {
        $this->logEvent($storeId, $userId, 'transaction_voided', (string)$transactionId, [
            'transaction_id' => $transactionId,
            'void_reason'    => $reason,
        ]);
    }

    /**
     * Helper internal untuk menyimpan log event keamanan forensik.
     */
    protected function logEvent(int $storeId, int $userId, string $action, ?string $refNo, array $metadata): void
    {
        try {
            if (!Schema::hasTable('pos_security_audit_logs')) {
                return;
            }

            DB::table('pos_security_audit_logs')->insert([
                'store_id'   => $storeId,
                'user_id'    => $userId,
                'action'     => $action,
                'ref_no'     => $refNo,
                'metadata'   => json_encode($metadata),
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::warning("[POS SECURITY AUDIT] Action: {$action} by User #{$userId} on Store #{$storeId}", $metadata);
        } catch (\Throwable $e) {
            Log::error("Failed to write POS security audit log: " . $e->getMessage());
        }
    }

    /**
     * Mengambil rekapitulasi anomali keamanan untuk panel audit eksekutif.
     *
     * @param int|null $storeId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getSecurityAnomalies(?int $storeId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            if (!Schema::hasTable('pos_security_audit_logs')) {
                return [
                    'stats' => [
                        'total_drawer_kicks'       => 0,
                        'total_items_removed'      => 0,
                        'total_discount_overrides' => 0,
                        'total_voids'              => 0,
                    ],
                    'logs'  => [],
                ];
            }

            $query = DB::table('pos_security_audit_logs')
                ->join('users', 'users.id', '=', 'pos_security_audit_logs.user_id')
                ->select(
                    'pos_security_audit_logs.*',
                    'users.name as user_name',
                    'users.email as user_email'
                );

            if ($storeId) {
                $query->where('pos_security_audit_logs.store_id', $storeId);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('pos_security_audit_logs.created_at', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);
            }

            $logs = $query->orderBy('pos_security_audit_logs.id', 'desc')->limit(100)->get();

            $stats = [
                'total_drawer_kicks'       => $logs->where('action', 'drawer_open_no_sale')->count(),
                'total_items_removed'      => $logs->where('action', 'cart_item_removed')->count(),
                'total_discount_overrides' => $logs->where('action', 'discount_override')->count(),
                'total_voids'              => $logs->where('action', 'transaction_voided')->count(),
            ];

            return [
                'stats' => $stats,
                'logs'  => $logs,
            ];
        } catch (\Throwable $e) {
            return [
                'stats' => [
                    'total_drawer_kicks'       => 0,
                    'total_items_removed'      => 0,
                    'total_discount_overrides' => 0,
                    'total_voids'              => 0,
                ],
                'logs'  => [],
            ];
        }
    }
}
