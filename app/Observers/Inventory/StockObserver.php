<?php

namespace App\Observers\Inventory;

use App\Models\Admin\Setting;
use App\Models\Product\HistoryLogStock;
use App\Models\Product\PriceVariationStore;
use App\Models\Product\Stock;
use App\Models\Product\Variation;
use App\Models\Transaction\Transaction;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;

class StockObserver
{

    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }


    public function createData(Variation $variation, $warehouse = null)
    {

        $stocks = Stock::where('product_id', $variation->product_id)->where('variation_id', $variation->id)->where(function ($q) use ($warehouse) {
            return $warehouse != null ? $q->where("warehouse_id", $warehouse) : $q->where("warehouse_id", null);
        })->first();


        if ($stocks == null) {
            $stocks = Stock::create([
                'product_id'            => $variation->product_id,
                'variation_id'          => $variation->id,
                'warehouse_id'          => $warehouse,
                'qty_available'         => 0
            ]);
        }

        return $stocks;
    }

    public function createHistoryStock(String $condition, $data, $transactionId, Int $qty, Int $firstStock = 0, Int $endStock = 0)
    {
        $history = HistoryLogStock::create([
            'product_id'            => $data->product_id,
            'variation_id'          => $data->variation_id,
            'type'                  => $condition,
            'qty'                   => $qty,
            'unit_id'               => $data->variation->unit_id ?? null,
            'transaction_id'        => $transactionId,
            'item'                  => $data->id,
            'from'                  => $firstStock,
            'to'                    => $endStock
        ]);
    }

    public function historyLogStock(Transaction $transaction, $variationId)
    {
        return HistoryLogStock::where("transaction_id", $transaction->id)->where("variation_id", $variationId)->first();
    }

    public function updateHistoryLog(HistoryLogStock $history, $data, Int $qty, Int $endStock = 0)
    {
        $history->update([
            'qty'                   => $qty,
            'unit_id'               => $data->variation->unit_id ?? null,
            'to'                    => $endStock
        ]);


        foreach (HistoryLogStock::where("id", ">", $history->id)->where("variation_id", $history->variation_id)->get() as $logs) {
            $logs->update([
                'qty'                   => $logs->qty,
                'unit_id'               => $data->variation->unit_id ?? null,
                'from'                  => $endStock,
                'to'                    => $logs->operator_by_type == 'add' ? $endStock + $logs->qty : $endStock - $logs->qty
            ]);

            $endStock = $logs->to;
        }
    }

    public function updatePricing(Variation $variation, $date = null)
    {

        if (Setting::first(['stocking_system_type'])->stocking_system_type == 'averaging') {
            if ($variation->harga_modal) {

                $price  = averaging_price($variation, 0, $date);

                $variation->harga_modal->update([
                    'price'     =>  $price
                ]);

                $variation->update([
                    'purchase_price'    => $variation->modal_price
                ]);
            } else {
                PriceVariationStore::create([
                    'variation_id'  => $variation->id,
                    'price'         => averaging_price($variation)
                ]);
            }
        }
    }

    public function getHistory(Request $request)
    {
        return HistoryLogStock::where(function ($q) use ($request) {
            return $request->type ? $q->where("type", $request->type) : '';
        })->whereHas('transaction', function ($q) {
            return $q->where("store_id", my_store());
        })->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('transaction', function ($q) use ($request) {
                $request->name ? $q->where('ref_no', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('transaction.store', function ($q) use ($request) {
                $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->product ? $query->where('id', $request->product) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('variation', function ($query) use ($request) {
                $request->variation ? $query->where('id', $request->variation) : '';
            });
        })->whereHas('transaction', function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : "";
            }
        })->orderBy("created_at", 'desc');
    }

    public function getData(Request $request)
    {
        return Stock::where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->product ? $query->where('id', $request->product) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('variation', function ($query) use ($request) {
                $request->variation ? $query->where('id', $request->variation) : '';
            });
        })->whereHas('product', function ($q) use ($request) {
            return $request->category ? $q->where('category_id', $request->category) : '';
        });
    }

    public function getAlert(Request $request)
    {
        return  Stock::with("product", "variation")->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) {
            $q->whereHas('product', function ($query) {
                return  $query->whereRaw("alert_quantity >= stocks.qty_available");
            })->whereHas("product", function ($q) {
                return $q->where("alert_quantity", ">", 0);
            });
        })->whereHas('product', function ($q) use ($request) {
            return $request->category ? $q->where('category_id', $request->category) : '';
        });
    }

    public function getAlertQty(Request $request)
    {
        return Stock::with(['variation'])->whereHas('variation.product', function ($q) {
            return $q->where('is_active', 'yes')->where('is_stock', 'yes');
        })->where(function ($q) use ($request) {
            $q->whereHas('variation.product', function ($query) {
                return  $query->whereRaw("alert_quantity >= stocks.qty_available");
            });
        })->where(function ($q) use ($request) {
            return $request->type == 'minus' ? $q->where('qty_available', '<', 0)  : $q->where('qty_available', '>', 0);
        });
    }
}
