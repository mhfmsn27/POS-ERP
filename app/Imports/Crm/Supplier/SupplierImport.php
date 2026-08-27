<?php

namespace App\Imports\Crm\Supplier;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function rules(): array
    {
        return [
            'nama'                      => 'required',
            'gunakan_akuntansi'         => 'required',
            'default_harga_penjualan'   => 'required', 
            'pajak'                     => 'required'
        ];
    }

    public function model(array $rows)
    {
    }
}
