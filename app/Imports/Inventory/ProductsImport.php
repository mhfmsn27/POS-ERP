<?php

namespace App\Imports\Inventory;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{

    public function rules(): array
    {
        return [
            'nama_produk'           => 'required', 
            'kategori'              => 'required',
            'tipe_barcode'          => 'required',
            'harga_jual'            => 'required',
            'tipe_produk'           => 'required',
        ];
    }

    public function model(array $rows)
    {
    }
}
