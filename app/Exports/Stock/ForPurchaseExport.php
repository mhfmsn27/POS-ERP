<?php

namespace App\Exports\Stock;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ForPurchaseExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    { 
        return view('admin.export.reports.stock', [
            'data' => $this->data,
        ]);
    }
}
