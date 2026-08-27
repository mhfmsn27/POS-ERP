<?php

namespace App\Imports\Master;

use App\Models\Product\Unit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UnitSheetImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
 

    public function model(array $row)
    {
        if ($row['id'] != null) {
            if ($row['unit_name'] == null || $row['unit_code'] == null) {
                Validator::make($row, [
                    'id'        => 'required|unique:units,id',
                    'unit_name'    => 'required',
                    'unit_code'    => 'required'
                ])->validate();
            }
    
            if($row['parent_id'] != null) {
                Validator::make($row, [
                    'value'    => 'required'
                ])->validate();
            }
    
            $parent = $row['parent_id'] != null ? 1 : 0;
            $parentID = $row['parent_id'] != null ? $row['parent_id'] : null;
            $value = $row['parent_id'] != null ? $row['value'] : 1;
    
            return new Unit([
                'id'    => $row['id'],
                'name'  => $row['unit_name'],
                'code'  => $row['unit_code'],
                'is_root_parent'    => $parent,
                'parent_id' => $parentID,
                'value'     => $value
            ]);
        } 
    }
}
