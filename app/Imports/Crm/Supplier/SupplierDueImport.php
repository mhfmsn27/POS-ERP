<?php

namespace App\Imports\Crm\Supplier;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierDueImport implements ToModel, WithHeadingRow, WithValidation
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
            'tanggal'                   => 'required|date', 
        ];
    }

    public function model(array $rows)
    {
    }
}
