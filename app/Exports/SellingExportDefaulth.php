<?php

namespace App\Exports;

use App\Helper;
use App\Models\Transaction\Transaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SellingExportDefaulth implements FromView
{

    protected $our;
    protected $jumlahTotal;
    protected $jumlahHutang;
    protected $jumlahTerbayar;
    protected $jumlahProfit;
    protected $status;

    public function __construct($our, $jumlahTotal, $jumlahHutang, $jumlahTerbayar, $jumlahProfit,$status)
    {
        $this->our = $our;
        $this->jumlahTotal = $jumlahTotal;
        $this->jumlahHutang = $jumlahHutang;
        $this->jumlahTerbayar = $jumlahTerbayar;
        $this->jumlahProfit = $jumlahProfit;
        $this->status = $status;
    }

    public function view(): View
    {
        
        return view('admin.export.reports.selling', [
            'data' => $this->our,
            'jumlahTotal'   => $this->jumlahTotal,
            'jumlahHutang'  => $this->jumlahHutang,
            'jumlahTerbayar'    => $this->jumlahTerbayar,
            'jumlahProfit'  => $this->jumlahProfit,
            'status'    => $this->status
        ]);
    }
}
