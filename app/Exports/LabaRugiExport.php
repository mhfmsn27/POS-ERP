<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class LabaRugiExport implements FromView
{
    protected $data;
    protected $gross;
    protected $profit;
    protected $profitsell; 

    public function __construct($data, $gross, $profit, $profitsell)
    {
        $this->data = $data;
        $this->gross = $gross;
        $this->profit = $profit;
        $this->profitsell = $profitsell; 
    }

    public function view(): View
    {
        
        return view('admin.export.reports.profit_loss', [
            'data' => $this->data,
            'gross'   => $this->gross,
            'profit'  => $this->profit,
            'profitsell'    => $this->profitsell
        ]);
    }
}
