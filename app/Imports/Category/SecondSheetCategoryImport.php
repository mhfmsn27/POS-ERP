<?php

namespace App\Imports\Category;

use App\Models\Product\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SecondSheetCategoryImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */

    public function rules(): array
    {
        return [
            'id'        => 'required|unique:categories,id',
            'subcategory_name'      => 'required',
            'category_id'      => 'required',
        ];
    }

    public function model(array $row)
    {
        if ($row['id'] != null) {
            return new Category([
                'id'        => $row['id'],
                'name'      => $row['subcategory_name'],
                'is_root_parent'    => 0,
                'parent_id' => $row['category_id']
            ]);
        }
    }
}
