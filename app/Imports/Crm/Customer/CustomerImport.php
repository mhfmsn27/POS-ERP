<?php

namespace App\Imports\Crm\Customer;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerImport implements ToModel, WithHeadingRow, WithValidation
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
            'tipe'                      => 'required',
            'pajak'                     => 'required'
        ];
    }

    public function model(array $rows)
    {
    }
}
