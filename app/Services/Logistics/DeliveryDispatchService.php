<?php

namespace App\Services\Logistics;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DeliveryDispatchService
{
    /**
     * Menugaskan kurir / armada internal toko untuk mengirim pesanan.
     *
     * @param int $storeId
     * @param int $transactionId
     * @param string $driverName
     * @param string $driverPhone
     * @return array
     */
    public function assignDelivery(int $storeId, int $transactionId, string $driverName, string $driverPhone): array
    {
        if (!Schema::hasTable('delivery_dispatches')) {
            return ['status' => false, 'message' => 'Tabel pengiriman belum aktif.'];
        }

        $id = DB::table('delivery_dispatches')->insertGetId([
            'store_id'       => $storeId,
            'transaction_id' => $transactionId,
            'driver_name'    => $driverName,
            'driver_phone'   => $driverPhone,
            'status'         => 'assigned',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return [
            'status'      => true,
            'dispatch_id' => $id,
            'message'     => "Kurir {$driverName} berhasil ditugaskan untuk pengiriman pesanan #{$transactionId}."
        ];
    }

    /**
     * Merekam Bukti Pengiriman Elektronik (Electronic Proof of Delivery - ePOD) dan mengirim konfirmasi WhatsApp.
     *
     * @param int $dispatchId
     * @param string $recipientName
     * @param string|null $signatureUrl
     * @param string|null $photoUrl
     * @param string|null $notes
     * @return array
     */
    public function submitEpod(
        int $dispatchId,
        string $recipientName,
        ?string $signatureUrl,
        ?string $photoUrl,
        ?string $notes = null
    ): array {
        if (!Schema::hasTable('delivery_dispatches')) {
            return ['status' => false, 'message' => 'Tabel pengiriman belum aktif.'];
        }

        $dispatch = DB::table('delivery_dispatches')->where('id', $dispatchId)->first();
        if (!$dispatch) {
            return ['status' => false, 'message' => 'Data pengiriman tidak ditemukan.'];
        }

        DB::table('delivery_dispatches')->where('id', $dispatchId)->update([
            'status'             => 'delivered',
            'recipient_name'     => $recipientName,
            'epod_signature_url' => $signatureUrl,
            'epod_photo_url'     => $photoUrl,
            'recipient_notes'    => $notes,
            'delivered_at'       => now(),
            'updated_at'         => now(),
        ]);

        // Auto-finalize Transaction status upon successful delivery
        try {
            $trx = Transaction::withoutGlobalScopes()->with('customer')->find($dispatch->transaction_id);
            if ($trx) {
                if ($trx->status !== 'final') {
                    $trx->update(['status' => 'final']);
                }

                if (!empty($trx->customer->phone)) {
                    $msg = "Halo Kak *{$trx->customer->name}*,\n\n"
                        . "Pesanan *#{$trx->ref_no}* telah *BERHASIL DITERIMA* oleh *{$recipientName}*.\n\n"
                        . "🚚 Kurir: *{$dispatch->driver_name}*\n"
                        . "⏰ Waktu Terima: " . now()->format('d/m/Y H:i') . " WIB\n\n"
                        . "Terima kasih telah mempercayai layanan kami!";
                    SendWhatsappDigitalReceiptJob::dispatch($trx->customer->phone, $msg);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("ePOD WA alert error: " . $e->getMessage());
        }

        return [
            'status'         => true,
            'recipient_name' => $recipientName,
            'delivered_at'   => now()->toDateTimeString(),
            'message'        => "Bukti serah terima (e-POD) berhasil disimpan & notifikasi WhatsApp terkirim."
        ];
    }
}
