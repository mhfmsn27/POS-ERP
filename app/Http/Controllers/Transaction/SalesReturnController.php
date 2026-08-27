<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\ReturnSellExport;
use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Models\Crm\SalesCommission;
use App\Models\Product\Stock;
use App\Models\Product\Unit;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\SalesReturn;
use App\Models\Transaction\Sell;
use App\Models\Transaction\SellPurchase;
use App\Models\Transaction\ShiftRegister;
use App\Models\Transaction\ShiftRegisterTransaction;
use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class SalesReturnController extends Controller
{

    public function index(Request $request)
    {

        if (!Auth::user()->can('Return Penjualan')) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $data = Transaction::where('type', 'sales_return')->orderBy('id', 'desc')
                ->where(function ($query) use ($request) {
                    return $request->store ? $query->where('store_id', $request->store) : '';
                })->where(function ($query) use ($request) {
                    return $request->start_date && $request->end_date ? $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
                })->where(function ($query) use ($request) {
                    return $request->store ?  $query->where('store_id', $request->store) : '';
                })->where(function ($query) use ($request) {
                    return $request->customer ? $query->where('customer_id', $request->customer) : '';
                })->where(function ($query) {
                    return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
                })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        if (Auth::user()->can("Detail Return Sales")) {
                            $html .= '<a class="dropdown-item" href="' . route('returnsell.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';
                        }

                        if (Auth::user()->can("Print Return Sales")) {
                            $html .= '<a class="dropdown-item" href="' . route('sell.print', $row->id) . '"><i class="fa fa-print"></i> ' . __('general.print') . ' </a>';
                        }

                        if ($row->due_total_return_sell > 0) {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="getpaymentmodal(this.id)"><i class="fa fa-money"></i> ' . __('general.add_payment') . ' </a>';
                        }

                        $html .= '</div></div></div>';
                        return $html;
                    }
                )->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at);
                })->addColumn('my_store', function ($row) {
                    return  $row->store->name ?? '';
                })->addColumn('my_cystomer', function ($row) {
                    return  $row->customer->name ?? '';
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
                    return number_format($row->due_total_return_sell);
                })->addColumn(
                    'my_return',
                    function ($row) {
                        return count($row->sellreturn);
                    }
                )->editColumn('final_total', function ($row) {
                    return number_format($row->final_total);
                })->rawColumns(['action',  'mydate', 'my_store', 'my_cystomer', 'my_return', 'final_total', 'my_payment_status', 'due_return'])
                ->make(true);
        }

        $store = Store::where(function ($query) {
            if (Auth::user()->store_id != 0) {
                return $query->where('id', Auth::user()->store_id);
            }
        })->get();

        return view('admin.returnsell.index', ['page' => __('sell.return_sell')], compact('store'));
    }

    public function getElement($id)
    {
        $data = Transaction::findOrFail($id);
        return response()->json([
            'max_amount' => $data->due_total_return_sell,
            'message' => 'success'
        ]);
    }


    public function bysell($id)
    {
        $data = Transaction::where("type", 'sell')->where("id", $id)->first();
        return view('admin.returnsell.create', ['page' => __('sidebar.sell_return')], compact('data'));
    }

    public function getProduct($id)
    {
        $parent = Transaction::findOrFail($id);
        $data['sales']  = array();
        foreach ($parent->sell as $sell) {
            $available_stock = $sell->qty - $sell->qty_return;
            if ($available_stock != 0) {
                $name = $sell->product->name ?? '';
                $variation = $sell->variation->name ?? '';

                $list = [
                    'id'    => $sell->id,
                    'name'  => $name . ' ' . $variation . ' (' . $available_stock . ')',
                    'stock' => $available_stock
                ];
                array_push($data['sales'], $list);
            }
        }
        return response()->json($data['sales']);
    }

    public function domItem($id)
    {
        $data = Sell::findOrFail($id);
        $name = $data->product->name ?? '';
        $variation = $data->variation->name ?? '';
        $available_stock = $data->qty - $data->qty_return;

        $unit = $data->unit ?? null;
        if ($data->unit_id != null) {
            $qtysell = $data->qty . ' Atau (' . $data->qty_into_unit . ') Dalam ' . $data->unit->name ?? '';
        } else {
            $qtysell = $data->qty;
        }

        $parent = $data->unit->parent_id ?? null;

        if ($parent == null) {
            $getUnit = Unit::where("id", $data->unit->id ?? 0)->orWhere("parent_id", $data->unit->id ?? 0)->get();
        } else {
            $getUnit = Unit::where("id", $data->unit->parent_id ?? 0)->orWhere("parent_id", $data->unit->parent_id ?? 0)->get();
        }


        if ($unit != null) {
            $listunit = array();
            foreach ($getUnit as $u) {
                if ($u->id != $data->unit_id) {
                    $i['id']    = $u->id;
                    $i['name']  = $u->name;
                    $i['value'] = $u->value;
                    $listunit[] = $i;
                }
            }
        } else {
            $listunit = $getUnit;
        }

        return response()->json([
            'product' => [
                'id_transaksi'  => $data->transaction_id,
                'name'      => $name . ' ' . $variation,
                'qty_sell'  => $qtysell,
                'sell_id'   => $data->id,
                'product_id' => $data->product->id,
                'var_id'    => $data->variation->id,
                's_price'   => number_format($data->unit_price),
                'price'     => $data->unit_price,
                'stock'     => $available_stock,
                'unit'      => $unit,
                'unitdown'      => $listunit
            ]
        ]);
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
                $getTransaksi = Transaction::where("type", "sales_return")->whereDate("created_at", date("Y-m-d"))->count() + 1;
                $invoiceNumber =  sprintf("%05d", $getTransaksi);
                $refNo = Helper::transactionKey('SL_RTN', $invoiceNumber);

                $data = new Transaction();
                $data->store_id = Session::get('mystore');
                $data->type     = 'sales_return';
                $data->status   = 'final';
                $data->payment_status = 'due';

                $getTransaction = Transaction::findOrFail($request->transaction_id);

                $data->return_parent = $getTransaction->id;
                $data->created_by   = Auth()->user()->id;
                $data->invoice_no   = $invoiceNumber;
                $request->ref_no ? $data->ref_no  = $request->ref_no : $data->ref_no = $refNo;
                $data->transaction_date = date('Y-m-d H:i:s');
                $data->customer_id  = $getTransaction->customer_id;
                $data->total_before_tax = $request->amount_total;
                $data->final_total = $request->amount_total;
                $data->save();

                $num = count($request->subtotal_return);
                $subtotal = 0;

                for ($x = 0; $x < $num; $x++) {

                    $qtyTotal = $request->qty_return[$x];
                    if ($request->unit_purchase[$x] != 0 || $request->unit_purchase[$x] != null || $request->unit_purchase[$x] != '0') {
                        $getUnits = Unit::where("id", $request->unit_purchase[$x])->first();
                        if ($getUnits) {
                            $qtyTotal = $request->qty_return[$x] * $getUnits->value;
                        }
                    }

                    $sell = Sell::findOrFail($request->sell_id[$x]);
                    $sell->qty_return       = $sell->qty_return + $qtyTotal;
                    $sell->save();

                    $subtotal = $sell->unit_price * $qtyTotal;

                    $sellpurchase = SellPurchase::where("sell_id", $request->sell_id[$x])->first();
                    $sellpurchase->qty_return = $sellpurchase->qty_return + $qtyTotal;
                    $sellpurchase->save();

                    if ($request->condition[$x] == 'good') {
                        $purchase = Purchase::where("id", $sellpurchase->purchase_id)->first();
                        $purchase->qty_return = $purchase->qty_return - $qtyTotal;
                        $purchase->save();

                        $stock = Stock::where("product_id", $request->product_id[$x])->where("variation_id", $request->variation_id[$x])->where("store_id", Session::get('mystore'))->first();
                        $stock->qty_available = $stock->qty_available + $qtyTotal;
                        $stock->save();
                    }

                    $return = new SalesReturn();
                    $return->transaction_id = $data->id;
                    $return->sell_id = $sell->id;
                    $return->return_qty = $qtyTotal;
                    $return->condition = $request->condition[$x];
                    if ($getUnits) {
                        $return->unit_id = $getUnits->id;
                    }
                    $return->save();

                    $storeSett = Store::findOrFail(Session::get('mystore'));

                    if ($storeSett->shift_register == 'active') {

                        $getShift = ShiftRegister::whereYear("created_at", date('Y'))
                            ->whereMonth("created_at", date('m'))
                            ->whereDay("created_at", date('d'))
                            ->where("status", "open")
                            ->where("store_id", Session::get('mystore'))
                            ->first();

                        if ($getShift != null) {
                            $shift = new ShiftRegisterTransaction();
                            $shift->shift_register_id = $getShift->id;
                            $shift->amount = $request->amount_total;
                            $method = 'cash';
                            $shift->pay_method = $method;
                            $shift->transaction_type = 'refund';
                            $shift->transaction_id = $data->id;
                            $shift->save();
                        }
                    }
                }

                if ($getTransaction->due_total == $data->due_total_return_sell) {
                    $data->payment_status = 'paid';
                    $getTransaction->payment_status = 'paid';
                }

                if ($getTransaction->due_total <= $data->due_total_return_sell) {
                    $getTransaction->payment_status = 'paid';
                }

                if ($getTransaction->due_total >= $data->due_total_return_sell) {
                    $data->payment_status = 'paid';
                }

                $getTransaction->save();
                $data->save();

                if ($getTransaction->commission != null) {
                    $getCommission = SalesCommission::findOrFail($getTransaction->commission->id);
                    $totalCommission = ($getCommission->commission_percentase / 100) * $subtotal;
                    $getCommission->commission_total_return = $getCommission->commission_total_return + $totalCommission;
                    if (($getCommission->commission_total_return + $totalCommission) >= $getCommission->commission_total) {
                        $getCommission->status = 'pay';
                    }

                    $getCommission->save();
                    $getTransaction->commission_contact_total = $getTransaction->commission_contact_total - $totalCommission;
                    $getTransaction->save();
                }

                return redirect()->route('returnsell.index')->with(['flash' => __('alert.created')]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }

        return $storeData;
    }

    public function download(Request $request)
    {

        $getTransaction = Transaction::where('type', 'sales_return')->orderBy('id', 'desc')
            ->where(function ($query) use ($request) {
                return $request->store ? $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->start_date && $request->end_date ? $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
            })->where(function ($query) use ($request) {
                return $request->store ?  $query->where('store_id', $request->store) : '';
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            });

        $data = $getTransaction->get();

        if ($request->excel == 'true') {
            return Excel::download(new ReturnSellExport($data), 'report_returnsell-' . $request->start_date . '.xlsx');
        } else {
            $pdf = PDF::loadView('admin.export.pdf.return_sell', compact('data'));
            return $pdf->stream('report_sell_return-' . $request->start_date . '.pdf');
        }
    }

    public function detail($id)
    {
        $return = Transaction::findOrFail($id);
        $condition = [
            'good'  => __('sell.good'),
            'broken' => __('sell.broken')
        ];
        return view('admin.returnsell.detail', ['page' => __('purchase.detail_return')], compact('return', 'condition'));
    }

    public function print($id)
    {
        $return = Transaction::findOrFail($id);
        $condition = [
            'good'  => __('sell.good'),
            'broken' => __('sell.broken')
        ];
        return view('admin.returnsell.print', ['page' => __('purchase.detail_return')], compact('return', 'condition'));
    }
}
