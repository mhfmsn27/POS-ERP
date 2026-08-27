<?php

namespace App\Exports\ReportDetails;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class PurchaseReturnExport implements FromView
{
    protected $data;
    protected $subtotal;
    public function __construct($data, $subtotal)
    {
        $this->data = $data;
        $this->subtotal = $subtotal;
    }

    public function view(): View
    {
        return view('admin.export.reports.detail.returnpurchase', [
            'data'  => $this->data,
            'subtotal'  => $this->subtotal
        ]);
    }
}
