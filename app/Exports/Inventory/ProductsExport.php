<?php

namespace App\Exports\Inventory;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView, ShouldQueue
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('export.products', [
            'data' => $this->data,
        ]);
    }

    public function chunkSize(): int
    {
        return 20;
    }
}
