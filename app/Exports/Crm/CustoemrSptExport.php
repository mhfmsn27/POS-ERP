<?php

namespace App\Exports\Crm;

use App\Observers\Crm\CustomerObserver;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class CustoemrSptExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    use Exportable;
    public $request;
    protected $customerObserver;

    public function __construct(Request $request, CustomerObserver $customerObserver)
    {
        $this->request              = $request;
        $this->customerObserver     = $customerObserver;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1   =>  [
                'font' => [
                    'bold'          => true,
                    'color'         => ['argb' => 'FFFFFF'],
                    'size'          => 11
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
        return $this->customerObserver->getData($this->request)->where(function ($q) {
            return $q->where("npwp", "!=", null)->orWhere("type", "bumn");
        })->orderBy("name", "asc");
    }

    public function headings(): array
    {
        return [
            'LT',
            'NPWP',
            'NAMA',
            'JALAN',
            'BLOK',
            'NOMOR',
            'RT',
            'RW',
            'KECAMATAN',
            'KELURAHAN',
            'KABUPATEN',
            'PROPINSI',
            'KODE_POS',
            'NOMOR_TELEPON'
        ];
    }

    /**
     * @param Customer $customer
     */
    public function map($customer): array
    {

        return [
            'LT',
            $customer->npwp ?? '-',
            $customer->name,
            $customer->address,
            '<BLOK>',
            '<NOMOR>',
            '0',
            '0',
            '<KECAMATAN>',
            '<KELURAHAN>',
            '<KABUPATEN>',
            '<PROPINSI>',
            '0',
            $customer->phone ?? 0
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 20,
            'D' => 20,
            'E' => 10,
            'F' => 10,
            'G' => 5,
            'H' => 5,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 15,
            'N' => 15
        ];
    }
}
