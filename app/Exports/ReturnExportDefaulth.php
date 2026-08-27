<?php

namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ReturnExportDefaulth implements FromView
{
    protected $data;
    protected $jumlahTotal;
    protected $jumlahHutang;
    public function __construct($data, $jumlahTotal, $jumlahHutang)
    {
        $this->data = $data;
        $this->jumlahTotal = $jumlahTotal;
        $this->jumlahHutang = $jumlahHutang;
    }

    public function view(): View
    {
        return view('admin.export.reports.return', [
            'data'  => $this->data,
            'jumlahTotal'   => $this->jumlahTotal,
            'jumlahHutang'  => $this->jumlahHutang
        ]);
    }
}
