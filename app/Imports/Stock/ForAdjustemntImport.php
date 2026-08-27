<?php

namespace App\Imports\Stock;

use App\Helper;
use App\Models\Product\Stock;
use App\Models\Product\Variation;
use App\Models\Stock\StockAdjusmentDetail;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ForAdjustemntImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function rules(): array
    {
        return [
            'kurangi_stok'    => 'required',
            'product_id'    => 'required',
            'harga_modal'    => 'required',
            'jumlah_stok'   => 'required'
        ];
    }

    public function collection(Collection $rows)
    {

        $total = 0;
        $recover = 0;
        foreach ($rows as $row) {

            if ($row['variation_id'] != null && $row['kurangi_stok'] > 0) {
                

                $getFrom = Stock::where('product_id', $row['product_id'])
                    ->where('variation_id', $row['variation_id'])
                    ->where('store_id', $this->data->store_id)
                    ->first();

                if ($getFrom->qty_available >= $row['kurangi_stok']) {
                    $total += Helper::fresh_aprice($row['harga_modal']) * $row['kurangi_stok'];

                    if ($row['dana_dipulihkan'] != null) {
                        $recover += Helper::fresh_aprice($row['dana_dipulihkan']);
                    }

                    $transfer = new StockAdjusmentDetail();
                    $transfer->transaction_id = $this->data->id;
                    $transfer->store_id       = $this->data->store_id;
                    $transfer->product_id     = $row['product_id'];
                    $transfer->variation_id   = $row['variation_id'];
                    $transfer->qty_adjustment   = $row['kurangi_stok'];
                    $transfer->unit_price       = Helper::fresh_aprice($row['harga_modal']);
                    $transfer->save();


                    $getFrom->qty_available = $getFrom->qty_available - $row['kurangi_stok'];
                    $getFrom->save();


                    $getPurchase = DB::select(DB::raw("SELECT s.*  
                    FROM (SELECT t.id, quantity, qty_sold, product_id, variation_id, t.store_id, SUM(IFNULL(t.qty_sold,0) + IFNULL(t.qty_adjusted,0) + IFNULL(t.qty_return,0) + IFNULL(t.qty_transfer,0) + IFNULL(t.qty_expire,0)) AS qty_sum
                    FROM purchases t  GROUP BY t.id, t.quantity, t.qty_sold, t.product_id, t.variation_id,t.store_id) AS s
                    WHERE s.quantity > s.qty_sum AND s.product_id=" . $row['product_id'] . " AND s.variation_id=" . $row['variation_id'] . "  AND s.store_id=" . $this->data->store_id . " ORDER BY s.id ASC "));

                    $totalQty = $row['kurangi_stok'];
                    foreach ($getPurchase as $p) {
                        $getPO = Purchase::find($p->id);
                        if ($getPO != null) {
                            $readyQty = $p->quantity - $p->qty_sum;

                            if ($readyQty >= $totalQty) {
                                $fixqty = $totalQty;
                                $totalQty -= $totalQty;
                            } else {
                                $fixqty = $readyQty;
                                $totalQty -= $fixqty;
                            }

                            $getPO->qty_sold      = $getPO->qty_sold + $fixqty;
                            $getPO->save();
                        }

                        if ($totalQty <= 0) {
                            break;
                        }
                    }
                }
            }
        }

        $update = Transaction::find($this->data->id);
        $update->total_before_tax = $total;
        $update->total_amount_recovered = $recover;
        $update->tax_amount       = 0;
        $update->final_total = $total;
        $update->save();
    }
}
