<?php

namespace App\Imports\Crm\Customer;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomerSaldoImport implements ToModel, WithHeadingRow, WithValidation
{
      /**
     * @param Collection $collection
     */
    public function rules(): array
    {
        return [
            'referensi'                 => 'required',
            'nominal'                   => 'required',
            'catatan'                   => 'required',
            'tanggal'                   => 'required|date'
        ];
    }

    public function model(array $rows)
    {
    }
}
