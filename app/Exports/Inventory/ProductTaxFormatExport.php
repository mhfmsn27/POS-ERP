<?php

namespace App\Exports\Inventory;

use App\Observers\Inventory\ProductObserver;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ProductTaxFormatExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{

    use Exportable;
    public $request;
    protected $productObserver;

    public function __construct(Request $request, ProductObserver $productObserver)
    {
        $this->request              = $request;
        $this->productObserver      = $productObserver;
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
        return $this->productObserver->getData($this->request);
    }

    public function headings(): array
    {
        return [
            'OB',
            'KODE_OBJEK',
            'NAMA',
            'HARGA_SATUAN',
        ];
    }

    /**
     * @param Product $product
     */
    public function map($product): array
    {

        return [
            'OB',
            $product->single_variant ? $product->single_variant->sku : '',
            $product->name,
            $product->single_variant ? (float)$product->single_variant->modal_price : 0,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
        ];
    }
}
