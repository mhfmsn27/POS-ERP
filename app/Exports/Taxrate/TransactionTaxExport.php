<?php

namespace App\Exports\Taxrate;

use App\Observers\Account\TaxObserver as AccountTaxObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionTaxExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;
    public $request;
    protected $taxObserver;

    public function __construct(Request $request, AccountTaxObserver $taxObserver)
    {
        $this->request              = $request;
        $this->taxObserver          = $taxObserver;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1   =>  [
                'font' => [
                    'bold'          => true,
                    'color'         => ['argb' => 'FFFFFF'],
                    'size'          => 14
                ],
                'fill' => [
                    'fillType'      => 'solid',
                    'startColor'    => ['rgb' => '0084ff'],
                ],
                'alignment' => [
                    'horizontal'    => Alignment::HORIZONTAL_CENTER,
                    'vertical'      => Alignment::VERTICAL_CENTER,
                    'wrapText'      => true,
                ],
            ],
        ];
    }

    public function query()
    {
        return $this->taxObserver->getData($this->request)->whereHas('transaction.customer', function ($q) {
            return $q->where('npwp', '!=', null)->where('npwp', "!=", '');
        });
    }

    public function headings(): array
    {
        return [
            ['FK', 'KD_JENIS_TRANSAKSI', 'FG_PENGGANTI', 'NOMOR_FAKTUR', 'MASA_PAJAK', 'TAHUN_PAJAK', 'TANGGAL_FAKTUR', 'NPWP', 'NAMA', 'ALAMAT_LENGKAP', 'JUMLAH_DPP', 'JUMLAH_PPN', 'JUMLAH_PPNBM', 'ID_KETERANGAN_TAMBAHAN', 'FG_UANG_MUKA', 'UANG_MUKA_DPP', 'UANG_MUKA_PPN', 'UANG_MUKA_PPNBM', 'REFERENSI', 'KODE_DOKUMEN_PENDUKUNG'],
            ['LT', 'NPWP', 'NAMA', 'JALAN', 'BLOK', 'NOMOR', 'RT', 'RW', 'KECAMATAN', 'KELURAHAN', 'KABUPATEN', 'PROPINSI', 'KODE_POS', 'NOMOR_TELEPON'],
            ['OF', 'KODE_OBJEK', 'NAMA', 'HARGA_SATUAN', 'JUMLAH_BARANG', 'HARGA_TOTAL', 'DISKON', 'DPP', 'PPN', 'TARIF_PPNBM', 'PPNBM'],
        ];
    }

    /**
     * @param Sales $sales
     */
    public function map($sales): array
    {

        $transactions = [];

        $transaction = $sales->transaction ?? null;

        if ($transaction) {
            $date           = substr($transaction->transaction_date, 0, 10);
            $carbonDate     = Carbon::createFromFormat('Y-m-d', $date);
            $customerOption = $transaction->customer->tax_default ?? 'no';
            // Add transaction data
            $transactions[] = [
                'FK',
                '01',
                '0',
                preg_replace("/[^0-9]/", "", $transaction->no_tax_ref->number ?? '0'),
                substr($transaction->transaction_date, 5, 2),
                substr($transaction->transaction_date, 0, 4),
                $carbonDate->format('d/m/Y'),
                preg_replace("/[^0-9]/", "", $transaction->customer->npwp ?? '0'),
                $transaction->customer->name ?? '',
                $transaction->customer->address ?? '',
                (int)($customerOption == 'yes' ? ($transaction->total_before_tax - ($transaction->tax_final + $transaction->discount_final)) : $transaction->total_before_tax - $transaction->discount_final),
                (int)$transaction->tax_final,
                '0',
                '0',
                '0',
                '0',
                '0',
                '0',
                'No Invoice :' . $transaction->ref_no,
                '0'
            ];

            // Add sales data

            foreach ($transaction->sell as $sell) {

                $totalDiscount  = (float)($sell->disc_amount + $sell->discount_subtotal);
                $dpp            = (float)(($sell->qty - $sell->qty_return) * ($customerOption == 'yes' ? ($sell->unit_price - $sell->tax_total) : $sell->unit_price));
                $transactions[] = [
                    'OF',
                    $sell->variation->sku ?? '',
                    $sell->variation->full_name ?? '',
                    (float)($customerOption == 'yes' ? ($sell->unit_price - $sell->tax_total) : $sell->unit_price),
                    (int)($sell->qty - $sell->qty_return),
                    $dpp,
                    $totalDiscount > 0 ? $totalDiscount : '0',
                    (float)($dpp - $totalDiscount),
                    (float)($sell->tax_total * ($sell->qty - $sell->qty_return)),
                    '0',
                    '0',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    ''
                ];
            }
        }


        return $transactions;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 20,
        ];
    }
}
