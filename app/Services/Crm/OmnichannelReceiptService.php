<?php

namespace App\Services\Crm;

use App\Jobs\SendWhatsappDigitalReceiptJob;
use App\Models\Transaction\ShiftRegister;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class OmnichannelReceiptService
{
    /**
     * Mengambil konfigurasi aktif WhatsApp Gateway (CRMHUB Omnichannel / SenderWA).
     *
     * @param int|null $storeId
     * @return object
     */
    public function getGatewayConfig(?int $storeId = null): object
    {
        $config = null;
        if (Schema::hasTable('omnichannel_gateway_settings')) {
            $query = DB::table('omnichannel_gateway_settings');
            if ($storeId) {
                $query->where('store_id', $storeId);
            }
            $config = $query->first();
        }

        return (object)[
            'provider'           => $config->provider ?? env('WA_GATEWAY_PROVIDER', 'crmhub_omnichannel'),
            'gateway_url'        => $config->gateway_url ?? env('OMNICHANNEL_WA_URL', 'http://127.0.0.1:8000/api/whatsapp/send'),
            'api_token'          => $config->api_token ?? env('OMNICHANNEL_API_TOKEN', ''),
            'enable_receipt'     => $config ? (bool)$config->enable_digital_receipt : true,
            'enable_z_report_wa' => $config ? (bool)$config->enable_shift_z_report_wa : true,
            'manager_phone'      => $config->manager_phone ?? env('STORE_MANAGER_WA', ''),
        ];
    }

    /**
     * Membuat dan mengirimkan Struk Digital Transaksi POS ke WhatsApp Pelanggan.
     *
     * @param int $transactionId
     * @param string|null $overridePhone
     * @return array
     */
    public function sendDigitalReceipt(int $transactionId, ?string $overridePhone = null): array
    {
        $transaction = Transaction::withoutGlobalScopes()
            ->with(['store', 'customer', 'sell', 'sell.product', 'sell.variation'])
            ->find($transactionId);

        if (!$transaction) {
            return ['status' => false, 'message' => 'Transaksi tidak ditemukan.'];
        }

        $recipientPhone = $overridePhone ?: ($transaction->customer->phone ?? null);
        if (empty($recipientPhone)) {
            return ['status' => false, 'message' => 'Nomor WhatsApp pelanggan tidak tersedia.'];
        }

        $cfg = $this->getGatewayConfig($transaction->store_id);
        if (!$cfg->enable_receipt) {
            return ['status' => false, 'message' => 'Fitur nota digital WhatsApp sedang dinonaktifkan.'];
        }

        // Susun Teks Struk Digital WhatsApp yang Rapi & Profesional
        $storeName = $transaction->store->name ?? 'POSHUB Store';
        $storeAddress = $transaction->store->address ?? '';
        $dateFormatted = date('d/m/Y H:i', strtotime($transaction->created_at));
        $refNo = $transaction->ref_no ?? ('TRX-' . $transaction->id);

        $msg = "🧾 *NOTA PEMBELIAN RESMI*\n";
        $msg .= "🏪 *{$storeName}*\n";
        if ($storeAddress) {
            $msg .= "📍 {$storeAddress}\n";
        }
        $msg .= "------------------------------------\n";
        $msg .= "No. Faktur : `{$refNo}`\n";
        $msg .= "Tanggal    : {$dateFormatted}\n";
        $msg .= "Pelanggan  : " . ($transaction->customer->name ?? 'Umum') . "\n";
        $msg .= "------------------------------------\n\n";

        $msg .= "*RINCIAN BELANJA:*\n";
        $items = $transaction->sell ?? [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $pName = $item->product->name ?? ($item->item_name ?? 'Produk');
                $qty = (float)$item->qty;
                $subtotal = number_format((float)$item->subtotal);
                $unitPrice = number_format((float)$item->unit_price);
                $msg .= "▪️ {$pName}\n";
                $msg .= "   {$qty}x @ Rp {$unitPrice} = *Rp {$subtotal}*\n";
            }
        }

        $grandTotal = number_format((float)$transaction->final_total);
        $msg .= "\n------------------------------------\n";
        $msg .= "💰 *TOTAL BAYAR : Rp {$grandTotal}*\n";
        $msg .= "💳 Status      : " . strtoupper($transaction->payment_status ?? 'PAID') . "\n";
        $msg .= "------------------------------------\n\n";
        $msg .= "🙏 _Terima kasih atas kunjungan Anda!_\n";
        $msg .= "Simpan nota digital ini sebagai bukti transaksi yang sah.";

        // Dispatch Job ke Background Queue
        SendWhatsappDigitalReceiptJob::dispatch(
            $recipientPhone,
            $msg,
            $cfg->gateway_url,
            $cfg->api_token,
            $cfg->provider
        );

        Log::info("Queued WhatsApp Digital Receipt for Transaction #{$transactionId} to {$recipientPhone}");

        return [
            'status'    => true,
            'recipient' => $recipientPhone,
            'message'   => "Nota digital berhasil dikirimkan ke WhatsApp {$recipientPhone}."
        ];
    }

    /**
     * Mengirimkan Notifikasi Ringkasan Z-Report Tutup Shift ke WhatsApp Manajer / Pemilik Toko.
     *
     * @param int $shiftId
     * @param string|null $overrideManagerPhone
     * @return array
     */
    public function sendShiftZReportToManager(int $shiftId, ?string $overrideManagerPhone = null): array
    {
        $shift = ShiftRegister::withoutGlobalScopes()
            ->with(['store', 'user'])
            ->find($shiftId);

        if (!$shift) {
            return ['status' => false, 'message' => 'Data shift register tidak ditemukan.'];
        }

        $cfg = $this->getGatewayConfig($shift->store_id);
        $phone = $overrideManagerPhone ?: ($cfg->manager_phone ?: ($shift->store->phone ?? null));

        if (empty($phone)) {
            return ['status' => false, 'message' => 'Nomor WhatsApp manajer/owner belum dikonfigurasi.'];
        }

        $storeName = $shift->store->name ?? 'POS Store';
        $cashierName = $shift->user->name ?? 'Kasir';
        $openCash = number_format((float)$shift->open_amount);
        $expectedCash = number_format((float)($shift->expected_cash_amount ?: $shift->open_amount));
        $actualCash = number_format((float)($shift->physical_cash_count ?: $shift->close_amount));
        $diff = (float)($shift->cash_difference ?: 0);
        $diffFormatted = ($diff >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($diff));
        $nonCash = number_format((float)$shift->other_amount);

        $msg = "📊 *LAPORAN TUTUP SHIFT KASIR (Z-REPORT)*\n";
        $msg .= "🏪 *{$storeName}*\n";
        $msg .= "Kasir       : {$cashierName}\n";
        $msg .= "Waktu Tutup : " . date('d/m/Y H:i:s') . "\n";
        $msg .= "------------------------------------\n";
        $msg .= "💵 Modal Awal     : Rp {$openCash}\n";
        $msg .= "🎯 Kas Seharusnya : Rp {$expectedCash}\n";
        $msg .= "📥 Kas Fisik Riil : Rp {$actualCash}\n";
        $msg .= "⚖️ Selisih Kas    : *{$diffFormatted}*\n";
        $msg .= "💳 Non-Tunai/QRIS : Rp {$nonCash}\n";
        $msg .= "------------------------------------\n";
        $msg .= "Catatan: " . ($shift->closing_notes ?: '-') . "\n";

        SendWhatsappDigitalReceiptJob::dispatch(
            $phone,
            $msg,
            $cfg->gateway_url,
            $cfg->api_token,
            $cfg->provider
        );

        return [
            'status'  => true,
            'message' => "Laporan Z-Report shift berhasil dikirim ke WhatsApp Owner ({$phone})."
        ];
    }
}
