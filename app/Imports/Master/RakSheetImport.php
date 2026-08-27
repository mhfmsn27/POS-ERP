<?php

namespace App\Imports\Master;

use App\Models\Product\Rak;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RakSheetImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */


    public function model(array $row)
    {
        if ($row['id'] != null) {
            if ($row['floor'] == null || $row['room'] == null || $row['rak']) {
                Validator::make($row, [
                    'id'        => 'required|unique:raks,id',
                    'floor'    => 'required',
                    'room'    => 'required',
                    'rak'    => 'required'
                ])->validate();
            }

            return new Rak([
                'id'    => $row['id'],
                'floor'  => $row['floor'],
                'room'  => $row['room'],
                'rak'    => $row['rak']
            ]);
        }
    }
}
