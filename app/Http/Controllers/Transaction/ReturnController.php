<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\ReturnExportDefaulth;
use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Models\Product\Stock;
use App\Models\Product\Supplier;
use App\Models\Product\Unit;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\ReturnDetail;
use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    public function index(Request $request)
    {

        if (!Auth::user()->can('Daftar Return')) {
            abort(403, 'Unauthorized action.');
        }

        $payment = [
            'due'   => __('general.po_due'),
            'paid'  => __('general.paid')
        ];

        if ($request->ajax()) {
            $data = Transaction::where(function ($query) use ($request) {
                return $request->store ?
                    $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->start_date ?
                    $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
            })->where(function ($query) use ($request) {
                return $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->where('type', 'purchase_return')->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        if (Auth::user()->can("Detail Return")) {
                            $html .= '<a class="dropdown-item" href="' . route('return.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';
                        }

                        if (Auth::user()->can("Print Return")) {
                            $html .= '<a class="dropdown-item" href="' . route('return.print', $row->id) . '"><i class="fa fa-print"></i> ' . __('general.print') . ' </a>';
                        }

                        if ($row->due_total_return > 0) {
                            if (Auth::user()->can("Tambah Pembayaran Return")) {
                                $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="getpaymentmodalReturn(this.id)"><i class="fa fa-money"></i> ' . __('general.add_payment') . ' </a>';
                            }
                        }

                        if (count($row->payment) > 0) {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="showPayment_(this.id)"><i class="fa fa-money"></i> Lihat Pembayaran </a>';
                        }

                        $html .= '</div></div></div>';
                        return $html;
                    }
                )->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at) . '<input type="hidden" id="idpo" value="' . $row->id . '">';
                })->addColumn('my_store', function ($row) {
                    return  $row->store->name ?? '';
                })->addColumn('my_supplier', function ($row) {
                    return  $row->supplier->name ?? '';
                })->addColumn('transaction_ref', function ($row) {
                    return $row->transaction->ref_no ?? '';
                })->addColumn('total_return', function ($row) {
                    return $row->qty_return . ' Qty Return';
                })->addColumn('total_nominal', function ($row) {
                    return number_format($row->final_total);
                })->addColumn(
                    'my_payment_status',
                    function ($row) {
                        $payment = [
                            'due'   => __('general.po_due'),
                            'paid'  => __('general.paid')
                        ];
                        $html =  '<span class=" badge bg-primary text-white">' . $payment[$row->payment_status] . '</span>';
                        return $html;
                    }
                )->addColumn('due_return', function ($row) {
                    return number_format($row->due_total_return);
                })
                ->rawColumns(['action', 'mydate', 'my_store', 'my_supplier', 'transaction_ref', 'total_return', 'total_nominal', 'my_payment_status'])
                ->make(true);
        }

        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        return view('admin.return.index', ['page' => __('sidebar.return')], compact('store'));
    }

    public function byPo($id)
    {
        $data = Transaction::findOrFail($id);
        return view('admin.return.po', ['page' => __('sidebar.return')], compact('data'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "qty_return"    => "required|array|min:0",
            "qty_return.*"  => "required|min:0",
            "subtotal_return"    => "required|array|min:0",
            "subtotal_return.*"  => "required|min:0",
            'amount_total'          => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
 
        try {
            $storeData = DB::transaction(function () use ($request) {
                $getTransaksi = Transaction::where("type", "purchase_return")->whereDate("created_at", date("Y-m-d"))->count() + 1;
                $invoiceNumber =  sprintf("%05d", $getTransaksi);
                $refNo = Helper::transactionKey('PO_RTN', $invoiceNumber);

                $data = new Transaction();
                $data->store_id = my_store();
                $data->type     = 'purchase_return';
                $data->status   = 'final';
                $data->payment_status = 'due';

                $getTransaction = Transaction::findOrFail($request->transaction_id);

                $data->supplier_id  = $getTransaction->supplier_id;
                $data->return_parent = $getTransaction->id;
                $data->created_by   = Auth()->user()->id;
                $data->invoice_no   = $invoiceNumber;
                $request->ref_no ? $data->ref_no  = $request->ref_no : $data->ref_no = $refNo;
                $data->transaction_date = date('Y-m-d H:i:s');

                $data->total_before_tax = $request->amount_total;
                $data->final_total = $request->amount_total;
                $data->save();

                $num = count($request->subtotal_return);
                for ($x = 0; $x < $num; $x++) {

                    $purchase = Purchase::findOrFail($request->p_id[$x]);

                    $getUnits = Unit::where("id", $request->unit_purchase[$x])->first();
                    if ($getUnits) {
                        $qtyReturn = $request->qty_return[$x] * $getUnits->value;
                    } else {
                        $qtyReturn = $request->qty_return[$x];
                    }

                    $poReturn = $purchase->qty_return + $qtyReturn;

                    if ($poReturn >= $purchase->quantity) {
                        $minGet = $poReturn - $purchase->quantity;
                        $poReturn = $poReturn - $minGet;
                        $qtyReturn = $poReturn;
                        $purchase->qty_return       = $poReturn;
                    } else {
                        $purchase->qty_return       = $poReturn;
                    }

                    $purchase->qty_return       = $qtyReturn;
                    $purchase->save();

                    if ($getTransaction->status == 'received') {
                        $CheckSkus = Stock::where('product_id', $purchase->product_id)->where('variation_id', $purchase->variation_id)->where('store_id', $purchase->store_id)->first();
                        $skus = Stock::findOrFail($CheckSkus->id);
                        $skus->qty_available  = $skus->qty_available -  $qtyReturn;
                        $skus->save();
                    }

                    $return = new ReturnDetail;
                    $return->transaction_id = $data->id;
                    $return->purchase_id = $purchase->id;
                    $return->return_qty = $qtyReturn;
                    if ($getUnits) {
                        $return->unit_id = $request->unit_purchase[$x];
                        $return->unit_qty = $request->qty_return[$x];
                    }
                    $return->save();
                }

                if ($getTransaction->due_total_po == $data->due_total_return) {
                    $data->payment_status = 'paid';
                    $getTransaction->payment_status = 'paid';
                }

                if ($getTransaction->due_total_po <= $data->due_total_return) {
                    $getTransaction->payment_status = 'paid';
                }

                if ($getTransaction->due_total_po >= $data->due_total_return) {
                    $data->payment_status = 'paid';
                }

                $getTransaction->save();
                $data->save();

                return redirect()->route('return.index')->with(['flash' => __('alert.created')]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }


        return $storeData;
    }

    public function getElement($id)
    {
        $data = Transaction::findOrFail($id);
        return response()->json([
            'max_amount' => $data->due_total_return,
            'message' => 'success'
        ]);
    }

    public function detail($id)
    {
        $return = Transaction::findOrFail($id);
        return view('admin.return.detail', ['page' => __('purchase.detail_return')], compact('return'));
    }

    public function print($id)
    {
        $return = Transaction::findOrFail($id);
        return view('admin.return.print', ['page' => __('purchase.detail_return')], compact('return'));
    }

    public function report(Request $request)
    {

        if (!Auth::user()->can('Laporan Return')) {
            abort(403, 'Unauthorized action.');
        }

        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        if ($request->ajax()) {
            $data = Transaction::where(function ($query) use ($request) {
                return $request->store ?
                    $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->start_date ?
                    $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->where('type', 'purchase_return')->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        if (Auth::user()->can("Detail Return")) {
                            $html .= '<a class="dropdown-item" href="' . route('return.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';
                        }

                        if (Auth::user()->can("Print Return")) {
                            $html .= '<a class="dropdown-item" href="' . route('return.print', $row->id) . '"><i class="fa fa-print"></i> ' . __('general.print') . ' </a>';
                        }

                        if ($row->due_total_return > 0) {
                            if (Auth::user()->can("Tambah Pembayaran Return")) {
                                $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="getpaymentmodalReturn(this.id)"><i class="fa fa-money"></i> ' . __('general.add_payment') . ' </a>';
                            }
                        }

                        if (count($row->payment) > 0) {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="showPayment_(this.id)"><i class="fa fa-money"></i> Lihat Pembayaran </a>';
                        }

                        $html .= '</div></div></div>';
                        return $html;
                    }
                )->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at) . '<input type="hidden" id="idpo" value="' . $row->id . '">';
                })->addColumn('my_store', function ($row) {
                    return  $row->store->name ?? '';
                })->addColumn('my_supplier', function ($row) {
                    return  $row->supplier->name ?? '';
                })->addColumn('transaction_ref', function ($row) {
                    return $row->transaction->ref_no ?? '';
                })->addColumn('total_return', function ($row) {
                    return $row->qty_return;
                })->addColumn('product_total', function ($row) {
                    return count($row->returndetail);
                })->addColumn('total_nominal', function ($row) {
                    return number_format($row->final_total);
                })->addColumn(
                    'my_payment_status',
                    function ($row) {
                        $payment = [
                            'due'   => __('general.po_due'),
                            'paid'  => __('general.paid')
                        ];
                        $html =  '<span class=" badge bg-primary text-white">' . $payment[$row->payment_status] . '</span>';
                        return $html;
                    }
                )->addColumn('due_return', function ($row) {
                    return number_format($row->due_total_return);
                })
                ->rawColumns(['action', 'mydate', 'my_store', 'my_supplier', 'transaction_ref', 'total_return', 'total_nominal', 'product_total', 'my_payment_status'])
                ->make(true);
        }

        return view('admin.reports.transaction.return', ['page' => __('sidebar.return_report')], compact('store'));
    }

    public function download(Request $request)
    {
        $getTransaction = Transaction::where('type', 'purchase_return')->orderBy('id', 'desc')
            ->where(function ($query) use ($request) {
                return $request->store ? $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->start_date && $request->end_date ? $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            });

        $data = $getTransaction->get();

        $jumlahTotal = 0;
        $jumlahHutang = 0;
        foreach ($data as $d) {
            $jumlahTotal += $d->final_total;
            $jumlahHutang += $d->due_total ?? $d->final_total;
        }

        $date = $request->start_date ?? 'all';

        if ($request->excel == 'true') {
            return Excel::download(new ReturnExportDefaulth($data, $jumlahTotal, $jumlahHutang), 'return_purchase_reports-' . $date . '.xlsx');
        } else {
            $pdf = PDF::loadView('admin.export.pdf.purchase_return', compact('data', 'jumlahTotal', 'jumlahHutang'))->setPaper('a4', 'landscape');
            return $pdf->stream('return_purchase_reports-' . $date . '.pdf');
        }
    }
}
