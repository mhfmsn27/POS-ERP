<?php

namespace App\Services\Security;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CashierFraudDetectorService
{
    /**
     * Mencatat temuan anomali transaksi kasir ke audit log dan memberi notifikasi ke Owner jika kritis.
     *
     * @param int $storeId
     * @param int|null $userId
     * @param string $cashierName
     * @param string $anomalyType
     * @param string $severity 'low', 'medium', 'high', 'critical'
     * @param array $details
     * @param string|null $ownerPhone
     * @return array
     */
    public function logAnomaly(
        int $storeId,
        ?int $userId,
        string $cashierName,
        string $anomalyType,
        string $severity = 'medium',
        array $details = [],
        ?string $ownerPhone = null
    ): array {
        if (!Schema::hasTable('cashier_fraud_audit_logs')) {
            return ['status' => false, 'message' => 'Tabel audit fraud belum aktif.'];
        }

        $id = DB::table('cashier_fraud_audit_logs')->insertGetId([
            'store_id'     => $storeId,
            'user_id'      => $userId,
            'cashier_name' => $cashierName,
            'anomaly_type' => $anomalyType,
            'severity'     => $severity,
            'details_json' => json_encode($details),
            'detected_at'  => now(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Kirim peringatan dini ke WhatsApp Owner jika tingkat bahaya High / Critical
        if (in_array($severity, ['high', 'critical']) && !empty($ownerPhone)) {
            try {
                $detailStr = json_encode($details, JSON_PRETTY_PRINT);
                $msg = "🚨 *PERINGATAN ANOMALI KASIR ({$severity})* 🚨\n\n"
                    . "👤 Kasir: *{$cashierName}*\n"
                    . "⚠️ Jenis Anomali: *{$anomalyType}*\n"
                    . "⏰ Waktu: " . now()->format('d/m/Y H:i') . "\n\n"
                    . "Detail: {$detailStr}\n\n"
                    . "Harap periksa rekaman CCTV / laporan audit kasir terkait.";
                SendWhatsappDigitalReceiptJob::dispatch($ownerPhone, $msg);
            } catch (\Throwable $e) {
                Log::warning("Fraud alert WA error: " . $e->getMessage());
            }
        }

        return [
            'status'   => true,
            'log_id'   => $id,
            'severity' => $severity,
            'message'  => "Anomali kasir {$anomalyType} ({$severity}) berhasil dicatat."
        ];
    }

    /**
     * Memindai transaksi kasir untuk mendeteksi potensi kecurangan otomatis.
     *
     * @param int $storeId
     * @param string|null $date
     * @return array
     */
    public function scanCashierAnomalies(int $storeId, ?string $date = null): array
    {
        $scanDate = $date ?? now()->format('Y-m-d');
        $anomaliesFound = [];

        // 1. Deteksi transaksi di luar jam operasional (23:00 - 05:00)
        $offHourTrx = DB::table('transactions')
            ->where('store_id', $storeId)
            ->whereDate('created_at', $scanDate)
            ->whereRaw("HOUR(created_at) >= 23 OR HOUR(created_at) <= 5")
            ->get();

        foreach ($offHourTrx as $trx) {
            $anomaliesFound[] = [
                'type'     => 'off_hour_transaction',
                'severity' => 'medium',
                'cashier'  => 'User ID ' . ($trx->created_by ?? 'Unknown'),
                'details'  => "Transaksi #{$trx->ref_no} dibuat di luar jam operasional (" . date('H:i', strtotime($trx->created_at)) . "). Total: Rp " . number_format($trx->final_total),
            ];
        }

        // 2. Deteksi diskon manual yang tidak wajar (Diskon > Rp 100.000)
        $excessDiscountTrx = DB::table('transactions')
            ->where('store_id', $storeId)
            ->whereDate('created_at', $scanDate)
            ->where('discount_amount', '>', 100000)
            ->get();

        foreach ($excessDiscountTrx as $trx) {
            $anomaliesFound[] = [
                'type'     => 'excess_discount',
                'severity' => 'high',
                'cashier'  => 'User ID ' . ($trx->created_by ?? 'Unknown'),
                'details'  => "Diskon manual bernilai besar pada transaksi #{$trx->ref_no}: Rp " . number_format($trx->discount_amount ?? 0),
            ];
        }

        return [
            'status'           => true,
            'scan_date'        => $scanDate,
            'total_anomalies'  => count($anomaliesFound),
            'anomalies'        => $anomaliesFound
        ];
    }
}
