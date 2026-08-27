<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
     /**
     * @param Collection $collection
     */
    public function rules(): array
    {
        return [
            'nama_produk'      => 'required',
            'sku'       => 'required',
            'kategori'       => 'required',
            'tipe_barcode'       => 'required',
            'harga_jual'       => 'required',
        ];
    }

    public function model(array $rows)
    {
        
    }
}
