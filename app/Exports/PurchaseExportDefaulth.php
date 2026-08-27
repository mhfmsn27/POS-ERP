<?php

namespace App\Exports;

use App\Helper;
use App\Models\Transaction\Transaction;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class PurchaseExportDefaulth implements FromView
{

    protected $data;
    protected $status;
    protected $payment;
    protected $jumlahTotal;
    protected $jumlahHutang;
    protected $jumlahTerbayar;

    public function __construct($data, $status, $payment, $jumlahTotal, $jumlahHutang, $jumlahTerbayar)
    {
        $this->data = $data;
        $this->status = $status;
        $this->payment = $payment;
        $this->jumlahTotal = $jumlahTotal;
        $this->jumlahHutang = $jumlahHutang;
        $this->jumlahTerbayar = $jumlahTerbayar;
    }

    public function view(): View
    {

        return view('admin.export.reports.purchase', [
            'data'  => $this->data,
            'status' => $this->status,
            'payment'   => $this->payment,
            'jumlahTotal'   => $this->jumlahTotal,
            'jumlahHutang'  => $this->jumlahHutang,
            'jumlahTerbayar'    => $this->jumlahTerbayar
        ]);
    }
}
