<?php

namespace App\Exports\ReportDetails;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class SaleExport implements FromView
{

    protected $data;
    protected $income;
    protected $subtotal;
    public function __construct($data, $income, $subtotal)
    {
        $this->data = $data;
        $this->income = $income;
        $this->subtotal = $subtotal;
    }

    public function view(): View
    {
        return view('admin.export.reports.detail.sale', [
            'data'  => $this->data,
            'income'   => $this->income,
            'subtotal'  => $this->subtotal
        ]);
    }
}
