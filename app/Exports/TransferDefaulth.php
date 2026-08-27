<?php

namespace App\Exports;

use App\Models\Transaction\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class TransferDefaulth implements FromView
{

    protected $jumlah;
    protected $data;
    protected $status;
    public function __construct($data,$jumlah,$status)
    {
        $this->data = $data;
        $this->jumlah = $jumlah;
        $this->status = $status;
    }

    public function view(): View
    {
        return view('admin.export.reports.transfer',[
            'data'  => $this->data,
            'jumlah' => $this->jumlah,
            'status'    => $this->status
        ]);
    }
}
