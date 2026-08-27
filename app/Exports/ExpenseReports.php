<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ExpenseReports implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    protected $jumlahTotal; 
    public function __construct($data, $jumlahTotal)
    {
        $this->data = $data;
        $this->jumlahTotal = $jumlahTotal; 
    }

    public function view(): View
    {
        return view('admin.export.reports.expense', [
            'data'  => $this->data,
            'jumlahTotal'   => $this->jumlahTotal, 
        ]);
    }
}
