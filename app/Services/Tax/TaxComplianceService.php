<?php

namespace App\Services\Tax;

use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TaxComplianceService
{
    /**
     * Memvalidasi format Nomor Pokok Wajib Pajak (NPWP) atau NIK 16-Digit Indonesia.
     *
     * @param string|null $taxId
     * @return array [valid: bool, formatted: string, type: string]
     */
    public function validateTaxId(?string $taxId): array
    {
        if (empty($taxId)) {
            return ['valid' => false, 'formatted' => '0000000000000000', 'type' => 'NONE'];
        }

        $clean = preg_replace('/[^0-9]/', '', $taxId);
        $length = strlen($clean);

        if ($length === 16) {
            return ['valid' => true, 'formatted' => $clean, 'type' => 'NIK_OR_NPWP16'];
        } elseif ($length === 15) {
            return ['valid' => true, 'formatted' => $clean, 'type' => 'NPWP15'];
        }

        return ['valid' => false, 'formatted' => $clean, 'type' => 'INVALID_LENGTH'];
    }

    /**
     * Menghasilkan file CSV Faktur Pajak Keluaran (FK) sesuai spesifikasi e-Faktur DJP Indonesia.
     *
     * @param string $taxPeriod Format YYYY-MM
     * @param int|null $storeId
     * @param int|null $userId
     * @return array
     */
    public function generateEfakturCsv(string $taxPeriod, ?int $storeId = null, ?int $userId = null): array
    {
        $year  = substr($taxPeriod, 0, 4);
        $month = substr($taxPeriod, 5, 2);

        $startDate = "{$year}-{$month}-01 00:00:00";
        $endDate   = date("Y-m-t 23:59:59", strtotime("{$year}-{$month}-01"));

        $query = Transaction::withoutGlobalScopes()
            ->with(['customer', 'store', 'sell'])
            ->where('type', 'sell')
            ->where('status', 'final')
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $transactions = $query->get();

        $csvRows = [];
        // Header Standar DJP e-Faktur
        $csvRows[] = "FK;KD_JENIS_TRANSAKSI;FG_PENGGANTI;NOMOR_FAKTUR;MASA_PAJAK;TAHUN_PAJAK;TANGGAL_FAKTUR;NPWP;NAMA;ALAMAT_LENGKAP;JUMLAH_DPP;JUMLAH_PPN;JUMLAH_PPNBM;ID_KETERANGAN_TAMBAHAN;FG_UANG_MUKA;UANG_MUKA_DPP;UANG_MUKA_PPN;UANG_MUKA_PPNBM;REFERENSI";

        $totalDPP = 0;
        $totalPPN = 0;
        $invoiceCount = 0;

        foreach ($transactions as $trx) {
            $customer = $trx->customer;
            $taxNumber = $customer ? ($customer->tax_number ?? ($customer->npwp ?? null)) : null;
            $taxValidation = $this->validateTaxId($taxNumber);
            $npwp = $taxValidation['valid'] ? $taxValidation['formatted'] : '0000000000000000';
            $custName = str_replace([';', "\n", "\r"], ' ', $customer ? ($customer->name ?? 'Pelanggan Umum') : 'Pelanggan Umum');
            $custAddr = str_replace([';', "\n", "\r"], ' ', ($customer && !empty($customer->address)) ? $customer->address : ($trx->store->address ?? 'Indonesia'));

            // Hitung DPP dan PPN (Default tarif PPN 11%)
            $finalTotal = (float)$trx->final_total;
            $dpp = round($finalTotal / 1.11, 2);
            $ppn = round($finalTotal - $dpp, 2);

            $dateFormatted = date('d/m/Y', strtotime($trx->created_at));
            $refNo = $trx->ref_no ?? ('INV-' . $trx->id);
            $nomorFaktur = '01000' . str_pad($trx->id, 8, '0', STR_PAD_LEFT);

            $csvRows[] = "FK;01;0;{$nomorFaktur};{$month};{$year};{$dateFormatted};{$npwp};{$custName};{$custAddr};{$dpp};{$ppn};0;0;0;0;0;0;{$refNo}";

            $totalDPP += $dpp;
            $totalPPN += $ppn;
            $invoiceCount++;
        }

        $csvContent = implode("\r\n", $csvRows);
        $fileName = "EFAKTUR-DJP-{$taxPeriod}-STORE-" . ($storeId ?? 'ALL') . ".csv";

        // Simpan log export jika tabel tersedia
        if (Schema::hasTable('efaktur_export_logs')) {
            DB::table('efaktur_export_logs')->insert([
                'store_id'       => $storeId,
                'tax_period'     => $taxPeriod,
                'total_invoices' => $invoiceCount,
                'total_dpp'      => $totalDPP,
                'total_ppn'      => $totalPPN,
                'file_name'      => $fileName,
                'exported_by'    => $userId ?? auth()->id() ?? 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return [
            'status'         => true,
            'tax_period'     => $taxPeriod,
            'total_invoices' => $invoiceCount,
            'total_dpp'      => $totalDPP,
            'total_ppn'      => $totalPPN,
            'file_name'      => $fileName,
            'csv_content'    => $csvContent,
            'message'        => "Export e-Faktur Masa {$taxPeriod} berhasil dibuat ({$invoiceCount} faktur pajak)."
        ];
    }
}
