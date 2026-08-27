<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\ReportDetails\PurchaseExport;
use App\Exports\ReportDetails\PurchaseReturnExport;
use App\Exports\ReportDetails\SaleExport;
use App\Exports\ReportDetails\SaleReturnExport;
use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use App\Models\Admin\Store;
use App\Models\Product\Supplier;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\ReturnDetail;
use App\Models\Transaction\SalesReturn;
use App\Models\Transaction\Sell; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ReportsController extends Controller
{
    public function sales(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        $customer = Customer::all();

        if ($request->ajax()) {

            $data = Sell::with("transaction", "product", "variation")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->where(function ($q) {
                return $q->whereHas('transaction', function ($query) {
                    $query->where('status', "final");
                });
            })->where(function ($q) {
                return $q->whereHas('transaction', function ($query) {
                    Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('product', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                })->orWhereHas('variation', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->store ? $query->where('store_id', $request->store) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->customer ? $query->where('customer_id', $request->customer) : '';
                });
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('detail', function ($row) {
                    $name = $row->product->name ?? '';
                    $variation = $row->variation->name ?? '';

                    $productName = $name . ' ' . $variation;
                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no ?? '';
                    $storeName = $row->transaction->store->name ?? '';
                    $transactionID = $row->transaction_id;
                    $qtySell = '';
                    $unitName = $row->unit->name ?? '';
                    if ($row->unit_id != null) {
                        $qtySell =   ' Atau (' . $row->qty_into_unit . ') Dalam ' . $unitName;
                    }
                    $html = '<p>
                                <b><i>' . $productName . '</i></b><br>
                                Tanggal Transaksi : ' . my_date($row->created_at) . '<br>
                                Invoice No : ' . $invoiceNo . ' <br> 
                                Nomor Ref : <a href="' . route('sell.detail', $transactionID) . '">' . $refNo . '</a> <br>
                                Qty DiJual : <b>' . number_format($row->qty) . ' ' . $qtySell . '</b><br>
                                Toko : ' . $storeName . '<br>
                            </p>';
                    return $html;

                    return '';
                })->addColumn('my_cystomer', function ($row) {
                    return  $row->transaction->customer->name ?? '';
                })->addColumn(
                    'qty_sale',
                    function ($row) {
                        return number_format($row->qty);
                    }
                )->addColumn(
                    'qty_return',
                    function ($row) {
                        return number_format($row->qty_return);
                    }
                )->addColumn('satuan', function ($row) {
                    return number_format($row->unit_price);
                })->addColumn('subtotal', function ($row) {
                    $allqty = $row->qty - $row->qty_return;
                    $subtotal = $row->unit_price_before_disc * $allqty;
                    return number_format($subtotal);
                })->addColumn('profit', function ($row) {
                    return number_format($row->profit_sales);
                })->addColumn('created_by', function ($row) {
                    return $row->transaction->createdby->name ?? '';
                })
                ->rawColumns(['satuan', 'detail',    'my_cystomer', 'subtotal',  'qty_sale', 'qty_return', 'profit', 'created_by'])
                ->make(true);
        }

        return view("admin.reports.detail.sales", ["page" => "Laporan Detail Penjualan"], compact("store", "customer"));
    }

    public function purchase(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        $supplier = Supplier::all();

        if ($request->ajax()) {

            $data = Purchase::with("transaction", "product", "variation")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->where(function ($q) use ($request) {
                return $q->whereHas('product', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                })->orWhereHas('variation', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                });
            })->where(function ($q) {
                return $q->whereHas('transaction', function ($query) {
                    Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->store ? $query->where('store_id', $request->store) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
                });
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        if ($row->expire_date != null) {
                            $now = date("Y-m-d");
                            $exp = $row->expire_date;
                            if ($now >= $exp) {
                                $total = ($row->qty_sold + $row->qty_adjusted) + ($row->qty_return + $row->qty_transfer) + $row->qty_expire;
                                if ($total < $row->quantity) {
                                    $html = '<a href="' . route('expire.claim', $row->id) . '" class="btn btn-sm btn-warning text-dark">Claim Expire</a>';
                                    return $html;
                                }
                            }
                        } else {
                            return '';
                        }
                    }
                )->addColumn('detail', function ($row) {
                    $name = $row->product->name ?? '';
                    $variation = $row->variation->name ?? '';

                    $productName = $name . ' ' . $variation;
                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no ?? '';
                    $storeName = $row->transaction->store->name ?? '';
                    $transactionID = $row->transaction_id;
                    $qtySell = '';
                    $unitName = $row->unit->name ?? '';
                    if ($row->unit_id != null) {
                        $qtySell =   ' Atau (' . $row->qty_into_unit . ') Dalam ' . $unitName;
                    }
                    $html = '<p>
                                <b><i>' . $productName . '</i></b><br>
                                Tanggal Transaksi : ' . substr($row->created_at, 0, 10) . '<br>
                                Invoice No : ' . $invoiceNo . ' <br> 
                                Nomor Ref : <a href="' . route('purchase.detail', $transactionID) . '">' . $refNo . '</a> <br>
                                Qty PO : <b>' . number_format($row->quantity) . ' ' . $qtySell . '</b><br>
                                Toko : ' . $storeName . '<br>
                                No Batch : ' . $row->no_batch . ' <br>
                                Tanggal Expire : ' . $row->expire_date . '
                            </p>';
                    return $html;

                    return '';
                })->addColumn(
                    'qty_po',
                    function ($row) {
                        return number_format($row->quantity);
                    }
                )->addColumn('satuan', function ($row) {
                    return number_format($row->purchase_price);
                })->addColumn('subtotal', function ($row) {
                    $allqty = $row->quantity - $row->qty_return;
                    $subtotal = $row->purchase_price * $allqty;
                    return number_format($subtotal);
                })->addColumn('qty_sold', function ($row) {
                    return number_format($row->qty_sold);
                })->addColumn('created_by', function ($row) {
                    return $row->transaction->createdby->name ?? '';
                })->editColumn('qty_return', function ($row) {
                    return number_format($row->qty_return);
                })->editColumn('qty_adjusted', function ($row) {
                    return number_format($row->qty_adjusted);
                })->editColumn('qty_transfer', function ($row) {
                    return number_format($row->qty_transfer);
                })->editColumn('qty_expire', function ($row) {
                    return number_format($row->qty_expire);
                })
                ->rawColumns(['satuan',  'action', 'detail',  'my_store', 'subtotal',  'qty_po', 'qty_sold', 'created_by', 'action'])
                ->make(true);
        }

        return view("admin.reports.detail.purchase", ["page" => "Laporan Detail PO / Pembelian"], compact("store", "supplier"));
    }

    public function return_sell(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        $customer = Customer::all();

        if ($request->ajax()) {

            $data = SalesReturn::with("transaction", "sell", "sell.product", "sell.variation")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->where(function ($q) use ($request) {
                return $q->whereHas('sell.product', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                })->orWhereHas('sell.variation', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                });
            })->where(function ($q) {
                return $q->whereHas('transaction', function ($query) {
                    Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->store ? $query->where('store_id', $request->store) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->customer ? $query->where('customer_id', $request->customer) : '';
                });
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('detail', function ($row) {
                    $name = $row->sell->product->name ?? '';
                    $variation = $row->sell->variation->name ?? '';
                    $productName = $name . ' ' . $variation;

                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no ?? '';
                    $storeName = $row->transaction->store->name ?? '';
                    $transactionID = $row->transaction_id;
                    $qtySell = '';
                    $unitName = $row->unit->name ?? '';
                    if ($row->unit_id != null) {
                        $qtySell =   ' Atau (' . $row->qty_into_unit . ') Dalam ' . $unitName;
                    }
                    $html = '<p>
                            <b><i>' . $productName . '</i></b><br>
                            Tanggal Transaksi : ' . my_date($row->created_at) . '<br>
                            Invoice No : ' . $invoiceNo . ' <br> 
                            Nomor Ref : <a href="' . route('returnsell.detail', $transactionID) . '">' . $refNo . '</a> <br>
                            Qty Return : <b>' . number_format($row->return_qty) . ' ' . $qtySell . '</b><br>
                            Toko : ' . $storeName . '<br>
                        </p>';
                    return $html;
                })
                ->addColumn('my_cystomer', function ($row) {
                    return  $row->transaction->customer->name ?? '';
                })->addColumn(
                    'qty_return',
                    function ($row) {
                        return number_format($row->return_qty);
                    }
                )->addColumn('satuan', function ($row) {
                    return number_format($row->sell->unit_price);
                })->addColumn('condition', function ($row) {
                    if ($row->condition == 'good') {
                        return "Baik / Masih Bagus";
                    } else {
                        return "Sudah Rusak";
                    }
                })->addColumn('subtotal', function ($row) {
                    $allqty =  $row->return_qty;
                    $subtotal = $row->sell->unit_price * $allqty;
                    return number_format($subtotal);
                })->addColumn('created_by', function ($row) {
                    return $row->transaction->createdby->name ?? '';
                })
                ->rawColumns(['satuan', 'detail',  'my_cystomer', 'subtotal',  'qty_return', 'created_by', 'condition'])
                ->make(true);
        }

        return view("admin.reports.detail.return_sales", ["page" => "Laporan Detail Return Penjualan"], compact("store", "customer"));
    }

    public function return_po(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        $supplier = Supplier::all();

        if ($request->ajax()) {

            $data = ReturnDetail::with("transaction", "purchase", "purchase.product", "purchase.variation")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->where(function ($q) {
                return $q->whereHas('transaction', function ($query) {
                    Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->store ? $query->where('store_id', $request->store) : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('purchase.product', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                })->orWhereHas('purchase.variation', function ($query) use ($request) {
                    $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
                });
            })->where(function ($q) use ($request) {
                return $q->whereHas('transaction', function ($query) use ($request) {
                    $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
                });
            })->where("return_qty", ">", 0)->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('detail', function ($row) {
                    $name = $row->purchase->product->name ?? '';
                    $variation = $row->purchase->variation->name ?? '';
                    $productName = $name . ' ' . $variation;

                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no ?? '';
                    $storeName = $row->transaction->store->name ?? '';
                    $transactionID = $row->transaction_id;
                    $qtySell = '';
                    $unitName = $row->unit->name ?? '';
                    if ($row->unit_id != null) {
                        $qtySell =   ' Atau (' . $row->qty_into_unit . ') Dalam ' . $unitName;
                    }
                    $html = '<p>
                        <b><i>' . $productName . '</i></b><br>
                        Tanggal Transaksi : ' . substr($row->created_at, 0, 10) . '<br>
                        Invoice No : ' . $invoiceNo . ' <br> 
                        Nomor Ref : <a href="' . route('return.detail', $transactionID) . '">' . $refNo . '</a> <br>
                        Qty Return : <b>' . number_format($row->return_qty) . ' ' . $qtySell . '</b><br>
                        Toko : ' . $storeName . '<br>
                    </p>';
                    return $html;
                })->addColumn('my_supplier', function ($row) {
                    return  $row->transaction->supplier->name ?? '';
                })->addColumn(
                    'qty_return',
                    function ($row) {
                        return number_format($row->return_qty);
                    }
                )->addColumn('satuan', function ($row) {
                    return number_format($row->purchase->purchase_price);
                })->addColumn('subtotal', function ($row) {
                    $allqty = $row->return_qty;
                    $subtotal = $row->purchase->purchase_price * $allqty;
                    return number_format($subtotal);
                })->addColumn('created_by', function ($row) {
                    return $row->transaction->createdby->name ?? '';
                })
                ->rawColumns(['satuan', 'detail', 'my_supplier', 'subtotal', 'qty_return', 'created_by'])
                ->make(true);
        }

        return view("admin.reports.detail.return_po", ["page" => "Laporan Detail Return PO / Pembelian"], compact("store", "supplier"));
    }

    // Export Detail
    public function exportSales(Request $request)
    {
        $data = Sell::with("transaction", "product", "variation")->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            }
            if ($request->date_now) {
                return $q->whereDate('created_at', $request->date_now);
            }
        })->where(function ($q) {
            return $q->whereHas('transaction', function ($query) {
                $query->where('status', "final");
            });
        })->where(function ($q) {
            return $q->whereHas('transaction', function ($query) {
                Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->store ? $query->where('store_id', $request->store) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->customer ? $query->where('customer_id', $request->customer) : '';
            });
        })->orderBy("id", "desc")->limit(5000)->get();

        $income = 0;
        $subtotal = 0;
        foreach ($data as $d) {
            $allqty = $d->qty - $d->qty_return;
            $subtotal_data = $d->unit_price_before_disc * $allqty;
            $income += (int)$d->profit_sales;
            $subtotal  += (int)$subtotal_data;
        }

        return Excel::download(new SaleExport($data, $income, $subtotal), 'laporan_penjualan_detail.xlsx');
    }

    public function exportPurchase(Request $request)
    {
        $data = Purchase::with("transaction", "product", "variation")->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            }
            if ($request->date_now) {
                return $q->whereDate('created_at', $request->date_now);
            }
        })->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) {
            return $q->whereHas('transaction', function ($query) {
                Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->store ? $query->where('store_id', $request->store) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
            });
        })->orderBy("id", "desc")->limit(5000)->get();

        $subtotal = 0;
        foreach ($data as $d) {
            $allqty = $d->quantity - $d->qty_return;
            $subtotal_data = $d->purchase_price * $allqty;
            $subtotal += $subtotal_data;
        }

        return Excel::download(new PurchaseExport($data, $subtotal), 'laporan_purchase_detail.xlsx');
    }

    public function exportSaleReturn(Request $request)
    {
        $data = SalesReturn::with("transaction", "sell", "sell.product", "sell.variation")->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            }
            if ($request->date_now) {
                return $q->whereDate('created_at', $request->date_now);
            }
        })->where(function ($q) use ($request) {
            return $q->whereHas('sell.product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('sell.variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) {
            return $q->whereHas('transaction', function ($query) {
                Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->store ? $query->where('store_id', $request->store) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->customer ? $query->where('customer_id', $request->customer) : '';
            });
        })->orderBy("id", "desc")->limit(5000)->get();

        $subtotal = 0;
        foreach ($data as $d) {
            $allqty =  $d->return_qty;
            $subtotal_data = $d->sell->unit_price * $allqty;
            $subtotal += $subtotal_data;
        }

        return Excel::download(new SaleReturnExport($data, $subtotal), 'laporan_sale_return_detail.xlsx');
    }

    public function exportPurchaseReturn(Request $request)
    {
        $data = ReturnDetail::with("transaction", "purchase", "purchase.product", "purchase.variation")->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            }
            if ($request->date_now) {
                return $q->whereDate('created_at', $request->date_now);
            }
        })->where(function ($q) {
            return $q->whereHas('transaction', function ($query) {
                Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->store ? $query->where('store_id', $request->store) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('purchase.product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('purchase.variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
            });
        })->where("return_qty", ">", 0)->orderBy("id", "desc")->limit(5000)->get();

        $subtotal = 0;
        foreach ($data as $d) {
            $allqty = $d->return_qty;
            $subtotal_data = $d->purchase->purchase_price * $allqty;
            $subtotal += $subtotal_data;
        }

        return Excel::download(new PurchaseReturnExport($data, $subtotal), 'laporan_purchase_return_detail.xlsx');
    }
}
