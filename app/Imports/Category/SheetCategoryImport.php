<?php

namespace App\Imports\Category;

use App\Models\Product\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SheetCategoryImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */

    public function rules(): array
    {
        return [
            'id'        => 'required|unique:categories,id',
            'category_name'      => 'required',
        ];
    }

    public function model(array $row)
    {
        if ($row['id'] != null) {
            return new Category([
                'id'        => $row['id'],
                'name'      => $row['category_name'],
                'is_root_parent'    => 1,
            ]);
        }
    }
}
