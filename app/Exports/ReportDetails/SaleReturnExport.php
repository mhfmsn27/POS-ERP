<?php

namespace App\Exports\ReportDetails;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class SaleReturnExport implements FromView
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
        return view('admin.export.reports.detail.returnsell', [
            'data'  => $this->data,
            'subtotal'  => $this->subtotal
        ]);
    }
}
