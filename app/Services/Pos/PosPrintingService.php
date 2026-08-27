<?php

namespace App\Services\Pos;

use App\Models\Transaction\Transaction;

class PosPrintingService
{
    /**
     * Menghasilkan Raw Binary ESC/POS Commands untuk printer thermal kasir (58mm / 80mm).
     *
     * @param int $transactionId
     * @param int $paperWidth Kolom: 32 (untuk 58mm) atau 48 (untuk 80mm)
     * @return array
     */
    public function generateReceiptEscPos(int $transactionId, int $paperWidth = 32): array
    {
        $trx = Transaction::withoutGlobalScopes()
            ->with(['customer', 'store', 'sell', 'payment'])
            ->find($transactionId);

        if (!$trx) {
            return ['status' => false, 'message' => 'Transaksi tidak ditemukan.'];
        }

        // ESC/POS Commands Constants
        $ESC = "\x1B";
        $GS  = "\x1D";

        $INIT        = $ESC . "@";
        $ALIGN_LEFT  = $ESC . "a\x00";
        $ALIGN_CTR   = $ESC . "a\x01";
        $ALIGN_RIGHT = $ESC . "a\x02";
        $BOLD_ON     = $ESC . "E\x01";
        $BOLD_OFF    = $ESC . "E\x00";
        $CUT_PAPER   = $GS . "V\x41\x00";
        $DRAWER_KICK = $ESC . "p\x00\x19\xFA";

        $storeName = strtoupper($trx->store->name ?? 'POSHUB STORE');
        $storeAddr = $trx->store->address ?? 'Indonesia';

        $raw = $INIT . $DRAWER_KICK;
        $raw .= $ALIGN_CTR . $BOLD_ON . $storeName . "\n" . $BOLD_OFF;
        $raw .= $storeAddr . "\n";
        $raw .= str_repeat('-', $paperWidth) . "\n";

        $raw .= $ALIGN_LEFT;
        $raw .= "No. Faktur : " . ($trx->ref_no ?? $trx->id) . "\n";
        $raw .= "Tanggal    : " . date('d/m/Y H:i', strtotime($trx->created_at)) . "\n";
        $raw .= "Pelanggan  : " . ($trx->customer->name ?? 'Umum') . "\n";
        $raw .= str_repeat('-', $paperWidth) . "\n";

        foreach ($trx->sell as $item) {
            $name = $item->product->name ?? 'Item';
            $qty  = (float)$item->qty;
            $prc  = (float)$item->unit_price;
            $sub  = number_format($qty * $prc);

            $raw .= $name . "\n";
            $line = "  " . $qty . " x " . number_format($prc);
            $spaces = max(1, $paperWidth - strlen($line) - strlen($sub));
            $raw .= $line . str_repeat(' ', $spaces) . $sub . "\n";
        }

        $raw .= str_repeat('-', $paperWidth) . "\n";
        $grandTotal = "TOTAL: Rp " . number_format($trx->final_total);
        $raw .= $ALIGN_RIGHT . $BOLD_ON . $grandTotal . "\n" . $BOLD_OFF;
        $raw .= str_repeat('-', $paperWidth) . "\n";

        $raw .= $ALIGN_CTR . "Terima Kasih Atas Kunjungan Anda!\n";
        $raw .= "Barang yang dibeli tidak dapat ditukar\n\n\n\n";
        $raw .= $CUT_PAPER;

        return [
            'status'         => true,
            'transaction_id' => $transactionId,
            'paper_width'    => $paperWidth,
            'base64_raw'     => base64_encode($raw),
            'message'        => 'Raw ESC/POS binary receipt berhasil dibuat.'
        ];
    }
}
