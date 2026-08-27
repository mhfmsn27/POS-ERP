<?php

namespace App\Exports;

use App\Models\Transaction\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class AdjustmentDefaulth implements FromView
{

    protected $data;
    protected $jumlah;

    public function __construct($data, $jumlah)
    {
        $this->data = $data;
        $this->jumlah = $jumlah;
    }

    public function view(): View
    {
        return view('admin.export.reports.adjustment', [
            'data'  => $this->data,
            'jumlah'    => $this->jumlah
        ]);
    }
}
