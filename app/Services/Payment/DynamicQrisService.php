<?php

namespace App\Services\Payment;

use App\Models\Transaction\Transaction;
use App\Services\Crm\OmnichannelReceiptService;
use App\Services\Pos\CustomerDisplayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DynamicQrisService
{
    /**
     * Menghasilkan tagihan QRIS Dinamis dengan nominal presisi untuk kasir POS.
     *
     * @param int $storeId
     * @param float $amount
     * @param int|null $transactionId
     * @return array
     */
    public function generateDynamicQris(int $storeId, float $amount, ?int $transactionId = null): array
    {
        if (!Schema::hasTable('qris_payment_transactions')) {
            return ['status' => false, 'message' => 'Tabel qris_payment_transactions belum tersedia.'];
        }

        $invoiceNumber = 'QRIS-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        $expiredAt = now()->addMinutes(15);

        // Standard EMVCo Dynamic QRIS Mock Payload
        $qrisPayload = "00020101021226600014ID.LINKAJA.WWW01189360091100000000000215{$invoiceNumber}51440014ID.DOKU.WWW0118936009110000000000520458125303360540" . strlen((int)$amount) . ((int)$amount) . "5802ID5910POSHUB STORE6007JAKARTA62170113{$invoiceNumber}6304ABCD";
        $qrisQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrisPayload);

        $id = DB::table('qris_payment_transactions')->insertGetId([
            'store_id'           => $storeId,
            'transaction_id'     => $transactionId,
            'invoice_number'     => $invoiceNumber,
            'amount'             => $amount,
            'qris_string'        => $qrisPayload,
            'qris_image_url'     => $qrisQrUrl,
            'status'             => 'pending',
            'payment_provider'   => 'dynamic_qris_gateway',
            'expired_at'         => $expiredAt,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return [
            'status'         => true,
            'qris_id'        => $id,
            'invoice_number' => $invoiceNumber,
            'amount'         => $amount,
            'amount_format'  => 'Rp ' . number_format($amount),
            'qris_image_url' => $qrisQrUrl,
            'expired_at'     => $expiredAt->toIso8601String(),
            'message'        => 'QRIS Dinamis berhasil dibuat. Silakan scan dengan aplikasi pembayaran.'
        ];
    }

    /**
     * Memproses Webhook Callback pembayaran QRIS dari Payment Gateway / Aggregator.
     *
     * @param array $payload
     * @return array
     */
    public function handleQrisCallback(array $payload): array
    {
        if (!Schema::hasTable('qris_payment_transactions')) {
            return ['status' => false, 'message' => 'Tabel QRIS belum aktif.'];
        }

        $invoiceNumber = $payload['invoice_number'] ?? ($payload['order_id'] ?? null);
        if (!$invoiceNumber) {
            return ['status' => false, 'message' => 'Invoice number tidak ditemukan pada callback payload.'];
        }

        $qrisRecord = DB::table('qris_payment_transactions')
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (!$qrisRecord) {
            return ['status' => false, 'message' => "Tagihan QRIS {$invoiceNumber} tidak ditemukan."];
        }

        return DB::transaction(function () use ($qrisRecord, $payload, $invoiceNumber) {
            $record = DB::table('qris_payment_transactions')
                ->where('id', $qrisRecord->id)
                ->lockForUpdate()
                ->first();

            if (!$record || $record->status === 'paid') {
                return ['status' => true, 'message' => 'Tagihan QRIS sudah lunas sebelumnya.'];
            }

            // Tandai QRIS sebagai Paid
            DB::table('qris_payment_transactions')->where('id', $record->id)->update([
                'status'             => 'paid',
                'paid_at'            => now(),
                'external_reference' => $payload['reference_id'] ?? ($payload['transaction_id'] ?? 'GATEWAY-REF'),
                'callback_payload'   => json_encode($payload),
                'updated_at'         => now(),
            ]);

            // Jika terhubung ke transaksi POS kasir, otomatis lunasi transaksi tersebut
            if ($record->transaction_id) {
                $trx = Transaction::withoutGlobalScopes()->find($record->transaction_id);
                if ($trx) {
                    $trx->payment_status = 'paid';
                    $trx->status = 'final';
                    $trx->save();

                    // 1. Update Layar Customer Display
                    try {
                        app(CustomerDisplayService::class)->updateDisplayState($trx->store_id, [
                            'status'         => 'thank_you',
                            'items'          => [],
                            'grand_total'    => (float)$trx->final_total,
                            'pay_amount'     => (float)$trx->final_total,
                            'change_amount'  => 0,
                        ]);
                    } catch (\Throwable $e) {}

                    // 2. Kirim Nota Digital WhatsApp
                    try {
                        app(OmnichannelReceiptService::class)->sendDigitalReceipt($trx->id);
                    } catch (\Throwable $e) {}
                }
            }

            Log::info("[QRIS WEBHOOK] Payment confirmed for invoice: {$invoiceNumber}, Amount: Rp {$record->amount}");

            return [
                'status'         => true,
                'invoice_number' => $invoiceNumber,
                'amount'         => (float)$record->amount,
                'message'        => 'Pembayaran QRIS berhasil dikonfirmasi dan transaksi otomatis lunas.'
            ];
        });
    }

    /**
     * Memeriksa status pelunasan QRIS secara real-time untuk polling frontend kasir.
     *
     * @param string $invoiceNumber
     * @return array
     */
    public function checkStatus(string $invoiceNumber): array
    {
        if (!Schema::hasTable('qris_payment_transactions')) {
            return ['status' => 'pending'];
        }

        $record = DB::table('qris_payment_transactions')
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if (!$record) {
            return ['status' => 'not_found'];
        }

        return [
            'status'         => $record->status,
            'invoice_number' => $record->invoice_number,
            'amount'         => (float)$record->amount,
            'paid_at'        => $record->paid_at,
            'is_paid'        => $record->status === 'paid',
        ];
    }
}
