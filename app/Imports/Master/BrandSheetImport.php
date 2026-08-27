<?php

namespace App\Imports\Master;

use App\Models\Product\Brand;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BrandSheetImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    public function rules(): array
    {
        return [
            'id'        => 'required|unique:brands,id',
            'brand_name'      => 'required',
            'brand_code'       => 'required',
        ];
    }

    public function model(array $row)
    {
        if ($row['id'] != null) {
            return new Brand([
                'id'    => $row['id'],
                'name'  => $row['brand_name'],
                'code'  => $row['brand_code']
            ]);
        }
    }
}
