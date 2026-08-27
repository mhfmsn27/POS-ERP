<?php

namespace App\Exports;

use App\Helper;
use App\Models\Transaction\Transaction;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class DueBookDefaulth implements FromView
{
    protected $data;
    protected $jumlahTotal;
    protected $jumlahHutang;
    protected $jumlahTerbayar;

    public function __construct($data, $jumlahTotal, $jumlahHutang, $jumlahTerbayar)
    {
        $this->data = $data;
        $this->jumlahTotal = $jumlahTotal;
        $this->jumlahHutang = $jumlahHutang;
        $this->jumlahTerbayar = $jumlahTerbayar;
    }

    public function view(): View
    {
        return view('admin.export.reports.due', [
            'data'  => $this->data,
            'jumlahTotal'   => $this->jumlahTotal,
            'jumlahHutang'  => $this->jumlahHutang,
            'jumlahTerbayar'    => $this->jumlahTerbayar
        ]);
    }
}
