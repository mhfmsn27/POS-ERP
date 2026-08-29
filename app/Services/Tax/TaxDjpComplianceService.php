<?php

namespace App\Services\Tax;

use App\Models\Admin\Customer;
use App\Models\Admin\Store;
use App\Models\Tax\TaxNoRef;
use App\Models\Tax\TaxNoRefDetail;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxDjpComplianceService
{
    /**
     * Generate format CSV Faktur Pajak Keluaran (FK) & Detail Transaksi (OF) standar DJP e-Faktur 3.0 / 3.2.
     *
     * @param array $transactionIds
     * @param int $storeId
     * @return array
     */
    public function generateEfakturCsv(array $transactionIds, int $storeId): array
    {
        $transactions = Transaction::withoutGlobalScopes()
            ->with(['customer', 'sell', 'sell.product', 'sell.variation', 'store'])
            ->whereIn('id', $transactionIds)
            ->where('store_id', $storeId)
            ->get();

        if ($transactions->isEmpty()) {
            return [
                'status'  => false,
                'message' => 'Tidak ada transaksi penjualan valid yang ditemukan untuk diekspor.'
            ];
        }

        $csvLines = [];

        // Header CSV e-Faktur DJP 3.2 Format
        $csvLines[] = "FK,KD_JENIS_TRANSAKSI,FG_PENGGANTI,NOMOR_FAKTUR,MASA_PAJAK,TAHUN_PAJAK,TANGGAL_FAKTUR,NPWP,NAMA,ALAMAT_LENGKAP,JUMLAH_DPP,JUMLAH_PPN,JUMLAH_PPNBM,ID_KETERANGAN_TAMBAHAN,FG_UANG_MUKA,UANG_MUKA_DPP,UANG_MUKA_PPN,UANG_MUKA_PPNBM,REFERENSI";
        $csvLines[] = "LT,NPWP,NAMA,JALAN,BLOK,NOMOR,RT,RW,KECAMATAN,KELURAHAN,KABUPATEN,PROPINSI,KODE_POS,NOMOR_TELEPON";
        $csvLines[] = "OF,KODE_OBJEK,NAMA,HARGA_SATUAN,JUMLAH_BARANG,HARGA_TOTAL,DISKON,DPP,PPN,TARIF_PPNBM,PPNBM";

        $totalDpp = 0;
        $totalPpn = 0;
        $countFaktur = 0;

        foreach ($transactions as $trx) {
            $customer = $trx->customer;
            $npwp = !empty($customer->tax_number) ? preg_replace('/[^0-9]/', '', $customer->tax_number) : '000000000000000';
            if (strlen($npwp) < 15) {
                $npwp = str_pad($npwp, 15, '0', STR_PAD_LEFT);
            }

            $namaCust   = !empty($customer->name) ? addslashes($customer->name) : 'Pelanggan Umum';
            $alamatCust = !empty($customer->address) ? addslashes($customer->address) : 'Indonesia';
            $tglFaktur  = date('d/m/Y', strtotime($trx->transaction_date ?? $trx->created_at));
            $masaPajak  = date('m', strtotime($trx->transaction_date ?? $trx->created_at));
            $tahunPajak = date('Y', strtotime($trx->transaction_date ?? $trx->created_at));

            // Alokasi / Ambil Nomor Seri Faktur Pajak (NSFP)
            $nsfp = $trx->no_tax ?? $this->allocateNsfp($storeId);
            $cleanNsfp = preg_replace('/[^0-9]/', '', $nsfp);
            if (empty($cleanNsfp)) {
                $cleanNsfp = '01000026' . str_pad((string)$trx->id, 8, '0', STR_PAD_LEFT);
            }

            // Hitung DPP & PPN (Tarif PPN 11% Indonesia)
            $finalTotal = (float)$trx->final_total;
            $dpp = (float)($trx->total_before_tax ?? round($finalTotal / 1.11, 2));
            $ppn = (float)($trx->tax_final ?? round($dpp * 0.11, 2));

            $totalDpp += $dpp;
            $totalPpn += $ppn;
            $countFaktur++;

            // Baris Header Faktur Keluaran (FK)
            $fkLine = [
                'FK',
                '01', // Jenis Transaksi: 01 (Kepada Pihak Bukan Pemungut PPN)
                '0',  // Faktur Pengganti: 0 (Normal)
                $cleanNsfp,
                $masaPajak,
                $tahunPajak,
                $tglFaktur,
                $npwp,
                $namaCust,
                $alamatCust,
                round($dpp),
                round($ppn),
                0,    // PPNBM
                '',   // ID Keterangan Tambahan
                '0',  // Uang Muka
                0,
                0,
                0,
                $trx->ref_no ?? ('INV-' . $trx->id)
            ];
            $csvLines[] = implode(',', $fkLine);

            // Baris Detail Item Barang/Jasa (OF)
            foreach ($trx->sell as $item) {
                $pName = $item->product->name ?? 'Barang Dagangan';
                $qty = (float)$item->qty;
                $unitPrice = (float)$item->unit_price;
                $disc = (float)($item->disc_amount ?? 0);
                $itemDpp = round(($unitPrice - $disc) * $qty / 1.11, 2);
                $itemPpn = round($itemDpp * 0.11, 2);

                $ofLine = [
                    'OF',
                    'BRG-' . ($item->product_id ?? 1),
                    addslashes($pName),
                    round($unitPrice / 1.11, 2),
                    $qty,
                    round(($unitPrice * $qty) / 1.11, 2),
                    round(($disc * $qty) / 1.11, 2),
                    round($itemDpp),
                    round($itemPpn),
                    0,
                    0
                ];
                $csvLines[] = implode(',', $ofLine);
            }
        }

        $csvContent = implode("\r\n", $csvLines);

        return [
            'status'         => true,
            'faktur_count'   => $countFaktur,
            'total_dpp'      => $totalDpp,
            'total_ppn'      => $totalPpn,
            'filename'       => 'efaktur_poshub_' . date('Ymd_His') . '.csv',
            'csv_content'    => $csvContent,
            'message'        => "Sukses menghasilkan file e-Faktur CSV untuk {$countFaktur} transaksi."
        ];
    }

    /**
     * Alokasikan Nomor Seri Faktur Pajak (NSFP) berikutnya dari Pool Toko.
     *
     * @param int $storeId
     * @return string
     */
    public function allocateNsfp(int $storeId): string
    {
        $year = date('y');
        $prefix = "010.000-{$year}.";

        // Cari di tabel TaxNoRef jika ada range aktif
        $pool = TaxNoRef::where('store_id', $storeId)->latest()->first();
        if ($pool && !empty($pool->end_no) && !empty($pool->start_no)) {
            $lastUsed = TaxNoRefDetail::where('tax_no_ref_id', $pool->id)->max('no');
            $nextNo = $lastUsed ? ($lastUsed + 1) : (int)$pool->start_no;

            if ($nextNo <= (int)$pool->end_no) {
                TaxNoRefDetail::create([
                    'tax_no_ref_id' => $pool->id,
                    'no'            => $nextNo,
                    'status'        => 'used',
                    'used_date'     => now()
                ]);
                return $prefix . str_pad((string)$nextNo, 8, '0', STR_PAD_LEFT);
            }
        }

        // Fallback generator NSFP terstandarisasi
        return $prefix . str_pad((string)mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung Potongan Pajak Penghasilan (PPh 21, PPh 23, PPh Final UMKM PP 23 / PP 55).
     *
     * @param float $grossAmount
     * @param string $taxType (pph21, pph23, pph_final_umkm)
     * @param bool $hasNpwp
     * @return array
     */
    public function calculateWithholdingTax(float $grossAmount, string $taxType, bool $hasNpwp = true): array
    {
        $rate = 0;
        $taxAmount = 0;
        $netAmount = $grossAmount;
        $description = '';

        switch (strtolower($taxType)) {
            case 'pph23':
            case 'pph_23':
                // PPh 23 Jasa: 2% (dengan NPWP) atau 4% (tanpa NPWP)
                $rate = $hasNpwp ? 0.02 : 0.04;
                $taxAmount = round($grossAmount * $rate, 2);
                $netAmount = $grossAmount - $taxAmount;
                $description = 'PPh Pasal 23 atas Jasa/Sewa (' . ($rate * 100) . '%)';
                break;

            case 'pph_final_umkm':
            case 'pph_umkm':
                // PPh Final PP 23/2018 (PP 55/2022): 0.5% dari Omset Bruto
                $rate = 0.005;
                $taxAmount = round($grossAmount * $rate, 2);
                $netAmount = $grossAmount - $taxAmount;
                $description = 'PPh Final UMKM PP 55/2022 (0.5%)';
                break;

            case 'pph21':
            case 'pph_21':
            default:
                // PPh 21 Bukan Pegawai Berkesinambungan (50% x Tarif Pasal 17)
                $rate = $hasNpwp ? 0.025 : 0.03;
                $taxAmount = round($grossAmount * $rate, 2);
                $netAmount = $grossAmount - $taxAmount;
                $description = 'PPh Pasal 21 atas Imbalan Jasa (' . ($rate * 100) . '%)';
                break;
        }

        return [
            'gross_amount' => $grossAmount,
            'tax_type'     => $taxType,
            'rate_percent' => $rate * 100,
            'has_npwp'     => $hasNpwp,
            'tax_amount'   => $taxAmount,
            'net_amount'   => $netAmount,
            'description'  => $description
        ];
    }
}
