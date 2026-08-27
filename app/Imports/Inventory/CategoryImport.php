<?php

namespace App\Imports\Inventory;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoryImport implements ToModel, WithHeadingRow, WithValidation
{
     /**
     * @param Collection $collection
     */
    public function rules(): array
    {
        return [
            'nama_kategori'      => 'required'
        ];
    }

    public function model(array $rows)
    {
    }
}
