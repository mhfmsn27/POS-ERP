<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\PurchaseExportDefaulth;
use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Models\Admin\Taxrate;
use App\Models\Product\Product;
use App\Models\Product\Stock;
use App\Models\Product\Supplier;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\ShiftRegister;
use App\Models\Transaction\ShiftRegisterTransaction;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {

        if (!Auth::user()->can('Daftar Purchase')) {
            abort(403, 'Unauthorized action.');
        }

        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        $supplier = Supplier::all();

        $status = [
            'received'      => __('purchase.received'),
            'ordered'       => __('purchase.ordered'),
            'pending'       => __('purchase.pending')
        ];

        $payment = [
            'due'   => __('general.po_due'),
            'paid'  => __('general.paid')
        ];

        if ($request->ajax()) {
            $data = Transaction::where(function ($query) use ($request) {
                return $request->store ?  $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
            })->where(function ($query) use ($request) {
                return $request->payment ?  $query->where('payment_status', $request->payment) : '';
            })->where(function ($query) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $query->whereDate('created_at', $request->date_now);
                }
            })->where(function ($query) use ($request) {
                return $request->status ?
                    $query->where('status', $request->status) : '';
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->where('type', 'purchase')->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        if (Auth::user()->can("Detail Purchase")) {
                            $html .= '<a class="dropdown-item" href="' . route('purchase.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';
                        }

                        if (Auth::user()->can("Print Purchase")) {
                            $html .= '<a class="dropdown-item" href="' . route('purchase.print', $row->id) . '"><i class="fa fa-print"></i> ' . __('general.print') . ' </a>';
                        }

                        if (Auth::user()->can("Produk Label")) {
                            $html .= '<a class="dropdown-item" href="' . route('barcode.purchase', $row->id) . '"><i class="fa fa-barcode"></i> ' . __('produk.print_label') . ' </a>';
                        }

                        if ($row->status == 'received') {
                            if (Auth::user()->can("Tambah Return")) {
                                $html .= '<a class="dropdown-item" href="' . route('return.po', $row->id) . '"><i class="fa fa-repeat"></i> ' . __('purchase.return') . ' </a>';
                            }
                            $html .= '<a class="dropdown-item" href="' . route('purchase.update', $row->id) . '"><i class="fa fa-edit"></i> Edit Data </a>';
                        }

                        if ($row->status != 'received') {
                            if (Auth::user()->can("Update Status Purchase")) {
                                $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="getstatusmodal(this.id)"><i class="fa fa-check-circle"></i> ' . __('general.change_status') . ' </a>';
                            }
                        }

                        if (count($row->payment) > 0) {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="showPayment_(this.id)"><i class="fa fa-money"></i> Lihat Pembayaran </a>';
                        }

                        if ($row->due_total_po != '0') {
                            if (Auth::user()->can("Update Status Purchase")) {
                                $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '"  onclick="getpaymentmodal_purchase(this.id)"><i class="fa fa-money"></i> ' . __('general.add_payment') . ' </a>';
                            }
                        }

                        $html .= '</div></div></div>';

                        return $html;
                    }
                )->addColumn('identity', function ($row) {
                    return  $row->id;
                })->addColumn('mydate', function ($row) {
                    return  substr($row->created_at, 0, 10) . '<input type="hidden" id="idpo" value="' . $row->id . '">';
                })->addColumn('my_store', function ($row) {
                    return  $row->store->name ?? '';
                })->addColumn('my_supplier', function ($row) {
                    return  $row->supplier->name ?? '';
                })->addColumn(
                    'my_status',
                    function ($row) use ($status) {
                        $html =  '<span class=" badge bg-primary text-white">' . $status[$row->status] . '</span>';
                        $returnqty = $row->purchase()->get()->sum('qty_return');
                        if ($returnqty > 0) {
                            $html .= '<span class=" badge bg-danger text-white">(' . $returnqty . ') Item Qty Returned</span>';
                        }
                        return $html;
                    }
                )->addColumn(
                    'my_payment_status',
                    function ($row) {
                        $payment = [
                            'due'   => __('general.po_due'),
                            'paid'  => __('general.paid')
                        ];
                        $html =  '<span class=" badge bg-primary text-white">' . $payment[$row->payment_status] . '</span>';
                        return $html;
                    }
                )->addColumn('total_pay', function ($row) {
                    return $row->pay_total;
                })->addColumn('due_total', function ($row) {
                    return number_format($row->due_total_po ?? $row->final_total);
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'identity', 'mydate', 'my_store', 'my_supplier', 'my_status', 'my_payment_status', 'total_pay', 'due_total'])
                ->make(true);
        }

        return view('admin.purchase.index', ['page' =>  __('sidebar.purchase')], compact('store', 'supplier', 'status', 'payment'));
    }

    public function create()
    {

        if (!Auth::user()->can('Tambah Purchase')) {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'supplier'      => Supplier::orderBy('name', 'desc')->get(),
            'taxrate'       => Taxrate::all(),
            'status'        => [
                'received'      => __('received'),
                'ordered'       => __('order'),
                'pending'       => __('pending')
            ],
            'payment_method' => [
                'cash'          => 'Cash',
                'bank_transfer' => 'Bank Transfer',
                'card'          => 'Card',
            ],
        ];

        return view('admin.purchase.create', ['page' => __('sidebar.add_purchase')], compact('data'));
    }

    public function update($id)
    {

        $data = Transaction::where("type", "purchase")->where("id", $id)->first();
        if ($data == null) {
            return redirect()->back();
        }

        return view("admin.purchase.update", ["page" => "Update PO / Pembelian"], compact("data"));
    }

    public function edit(Request $request, $id)
    {
        $data = Transaction::where("type", "purchase")->where("id", $id)->first();
        if ($data == null) {
            return redirect()->back();
        }

        $num = count($request->p_id);
        for ($x = 0; $x < $num; $x++) {
            $po = Purchase::find($request->p_id[$x]);
            if ($po != null) {
                $request->expire_date[$x] ? $po->expire_date = $request->expire_date[$x] : true;
                $request->no_batch[$x] ? $po->no_batch = $request->no_batch[$x] : true;
                $po->save();
            }
        }

        return redirect()->back()->with(['flash' => "Penyimpanan Data berhasil dilakukan"]);
    }

    public function getProduct(Request $request)
    {
        $response = array();
        if ($request->term != null) {
            $getdata = Product::where('name', 'like', '%' . $request->term . '%')->limit(20)->get();
            foreach ($getdata as $product) {
                foreach ($product->variant as $v) {
                    if ($product->type == 'single') {
                        $name = '';
                    } else {
                        $name = $v->name;
                    }
                    $response[] = [
                        'id'    => $v->id,
                        'name'  => $product->name . ' - ' . $name
                    ];
                }
            }
            return response()->json($response);
        } else {
            $getdata = Product::limit(20)->get();
            foreach ($getdata as $product) {
                foreach ($product->variant as $v) {
                    if ($product->type == 'single') {
                        $name = '';
                    } else {
                        $name = $v->name;
                    }
                    $response[] = [
                        'id'    => $v->id,
                        'name'  => $product->name . ' - ' . $name
                    ];
                }
            }
        }
        return response()->json($response);
    }

    public function store(Request $request)
    {

        if (!Auth::user()->can('Tambah Purchase')) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'supplier_id'        => 'required',
            'status'             => 'required',

            "qty"    => "required|array|min:0",
            "qty.*"  => "required|min:0",

            "unit_cost"    => "required|array|min:0",
            "unit_cost.*"  => "required|min:0",

            "discount_percent"    => "required|array|min:0",
            "discount_percent.*"  => "required|min:0",

            "unit_cost_adiscount"    => "required|array|min:0",
            "unit_cost_adiscount.*"  => "required|min:0",

            "selling_price"    => "required|array|min:0",
            "selling_price.*"  => "required|min:0",

            "g_price"    => "required|array|min:0",
            "g_price.*"  => "required|min:0",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $storeData = DB::transaction(function () use ($request) {
                $getTransaction = Transaction::where("type", "purchase")->whereDate("created_at", date("Y-m-d"))->count() + 1;
                $invoiceNumber =  sprintf("%05d", $getTransaction);
                $refNo = Helper::transactionKey('PO', $invoiceNumber);

                $data = new Transaction();
                $data->store_id = Session::get('mystore');
                $data->type     = 'purchase';
                $data->status   = $request->status;
                $data->payment_status = 'due';

                $data->supplier_id  = $request->supplier_id;
                $data->created_by   = Auth()->user()->id;
                $data->invoice_no   = $invoiceNumber;
                $request->ref_no ? $data->ref_no  = $request->ref_no : $data->ref_no = $refNo;
                $data->transaction_date = date('Y-m-d H:i:s');

                $jumlah = 0;
                foreach ($request->line_total as $total) {
                    $jumlah = +Helper::fresh_aprice($total);
                }

                $data->total_before_tax = $jumlah;
                $data->tax_amount       = $request->tax_po;

                if ($request->discount_amount) {
                    $data->discount_type    = $request->type_discount;
                    $data->discount_amount  = $request->discount_amount;
                }

                $request->shipping_details ? $data->shipping_details = $request->shipping_details : null;
                $request->shipping_charges ? $data->shipping_charges = Helper::fresh_aprice($request->shipping_charges) : null;
                $request->additional_note ? $data->additional_notes = $request->additional_note : null;
                $data->final_total = $request->net_total;
                $data->save();

                $num = count($request->line_total);
                for ($x = 0; $x < $num; $x++) {
                    $purchase = new Purchase();
                    $purchase->transaction_id = $data->id;
                    $purchase->store_id       = $data->store_id;
                    $purchase->product_id     = $request->product_id[$x];
                    $purchase->variation_id   = $request->variant_id[$x];


                    if ($request->product_unit[$x] != 0 || $request->product_unit[$x] != null || $request->product_unit[$x] != '0') {
                        $getUnits = Unit::where("id", $request->product_unit[$x])->first();
                        if ($getUnits) {
                            $purchase->quantity = $request->qty[$x] * $getUnits->value;
                            $purchase->unit_id = $getUnits->id;
                            $purchase->unit_qty = $request->qty[$x];
                        } else {
                            $purchase->quantity = $request->qty[$x];
                        }
                    } else {
                        $purchase->quantity = $request->qty[$x];
                    }

                    $purchase->discount_percent = $request->discount_percent[$x];
                    $purchase->purchase_price          = Helper::fresh_aprice($request->unit_cost_adiscount[$x]);
                    $purchase->without_discount        = Helper::fresh_aprice($request->unit_cost[$x]);
                    $purchase->purchase_price_inc_tax = Helper::fresh_aprice($request->unit_cost_atax[$x]);
                    $purchase->item_tax       = $request->tax_price[$x];
                    $purchase->save();

                    $getVariation = Variation::findOrFail($request->variant_id[$x]);
                    $getVariation->purchase_price = Helper::fresh_aprice($request->unit_cost[$x]);
                    $getVariation->save();

                    if ($request->status == 'received') {
                        $CheckSkus = Stock::where('product_id', $request->product_id[$x])->where('variation_id', $request->variant_id[$x])->where('store_id', Session::get('mystore'))->first();
                        if ($CheckSkus == null) {
                            $skus = new Stock();
                            $skus->qty_available          = $purchase->quantity;
                        } else {
                            $skus = Stock::findOrFail($CheckSkus->id);
                            $skus->qty_available          = $skus->qty_available +  $purchase->quantity;
                        }
                        $skus->product_id     = $request->product_id[$x];
                        $skus->variation_id   = $request->variant_id[$x];
                        $skus->store_id       = $data->store_id;
                        $skus->save();
                    }
                }

                if ($request->payment_amount) {
                    $payment = new TransactionPayment();
                    $payment->transaction_id    = $data->id;
                    $payment->created_by        = Auth::user()->id;
                    $payment->amount            = Helper::fresh_aprice($request->payment_amount);
                    $payment->method            = $request->payment_method;
                    $payment->transaction_type  = 'transaction';
                    $request->payment_note ? $payment->note = $request->payment_note : null;
                    $request->account_id ? $payment->account_id = $request->account_id : null;
                    if ($request->payment_method == 'bank_transfer') {
                        $request->no_rek ? $payment->no_rek = $request->no_rek : null;
                        $request->an ? $payment->an = $request->an : null;
                        $request->bank_id ? $payment->bank_id = $request->bank_id : null;
                    } else if ($request->payment_method == 'card') {
                        $request->card_number ? $payment->card_number = $request->card_number : null;
                        $request->card_holder_name ? $payment->card_holder_name = $request->card_holder_name : null;
                        $request->card_transaction_number ? $payment->card_transaction_number = $request->card_transaction_number : null;
                        $request->card_type ? $payment->card_type = $request->card_type : null;
                        $request->card_month ? $payment->card_month = $request->card_month : null;
                        $request->card_year ? $payment->card_year = $request->card_year : null;
                        $request->card_security ? $payment->card_security = $request->card_security : null;
                    }
                    $payment->save();

                    if (Helper::fresh_aprice($request->payment_amount) >= $data->final_total) {
                        $data->payment_status = 'paid';
                        $data->save();
                    }

                    if ($request->account_id) {
                        if ($request->payment_amount > 0) {
                            Helper::createAccount('credit', $request, $payment);
                        }
                    }
                }


                return redirect()->route('purchase.index')->with(['flash' => __('alert.created')]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }

        return $storeData;
    }

    public function purchasePay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_amount'        => 'required',
            'transaction_id'        => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'error'
                ]);
            }
        }

        $getTransaction = Transaction::findOrFail($request->transaction_id);

        if ($getTransaction->type == 'sell') {
            $condition = $getTransaction->due_total;
            $dbtOrCrd  = 'debit';
            if ($getTransaction->due_total == 0) {
                return response()->json([
                    'errors' => "Tidak Ada Tunggakan Untuk Transaksi ini",
                    'message' => 'nothing'
                ]);
            }
        } else if ($getTransaction->type == 'purchase') {
            $dbtOrCrd  = 'credit';
            $condition = $getTransaction->due_total_po;
        } else if ($getTransaction->type == 'purchase_return') {
            $dbtOrCrd  = 'debit';
            $condition = $getTransaction->due_total_return;
        } else if ($getTransaction->type == 'sales_return') {
            $dbtOrCrd  = 'credit';
            $condition = $getTransaction->due_total_return_sell;
        }

        $payment = new TransactionPayment();
        $payment->transaction_id    = $request->transaction_id;

        if ($condition >= Helper::fresh_aprice($request->payment_amount)) {
            $pay    = Helper::fresh_aprice($request->payment_amount);
        } else {
            $pay    = $condition;
        }

        if ($condition == $pay) {
            $getTransaction->payment_status = 'paid';
            $getTransaction->save();
        }
        $payment->created_by        = Auth::user()->id;
        $payment->amount            = $pay;
        $payment->method            = $request->payment_method;
        $payment->transaction_type  = 'transaction';
        $request->payment_note ? $payment->note = $request->payment_note : null;
        $request->account_id ? $payment->account_id = $request->account_id : null;
        if ($request->payment_method == 'bank_transfer') {
            $request->no_rek ? $payment->no_rek = $request->no_rek : null;
            $request->an ? $payment->an = $request->an : null;
            $request->bank_id ? $payment->bank_id = $request->bank_id : null;
        } else if ($request->payment_method == 'card') {
            $request->card_number ? $payment->card_number = $request->card_number : null;
            $request->card_holder_name ? $payment->card_holder_name = $request->card_holder_name : null;
            $request->card_transaction_number ? $payment->card_transaction_number = $request->card_transaction_number : null;
            $request->card_type ? $payment->card_type = $request->card_type : null;
            $request->card_month ? $payment->card_month = $request->card_month : null;
            $request->card_year ? $payment->card_year = $request->card_year : null;
            $request->card_security ? $payment->card_security = $request->card_security : null;
        }



        if ($getTransaction->type == 'sell') {
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
                    $shift->amount = $pay;

                    $method = 'other';

                    if ($payment->method == 'cash') {
                        $method = 'cash';
                    }

                    if ($payment->method == 'bank_transfer') {
                        $method = 'bank';
                    }

                    $shift->pay_method = $method;
                    $shift->transaction_type = 'sell';
                    $shift->transaction_id = $getTransaction->id;
                    $shift->save();
                }
            }
        }

        $payment->save();

        if ($request->account_id) {
            if ($request->payment_amount > 0) {
                Helper::createAccount($dbtOrCrd, $request, $payment);
            }
        }
    }

    public function showPayment($id)
    {
        $data = TransactionPayment::with('user', 'account')->where("transaction_id", $id)->where("transaction_type", "transaction")->get();

        $list = array();
        foreach ($data as $d) {
            $item['id']     = $d->id;
            $item['date']   = substr($d->created_at, 0, 10);
            $item['user']   = $d->user->name ?? '';
            $item['amount'] = number_format($d->amount);
            $item['method'] = $d->payment_methode;
            $item['account'] = $d->account->account->name ?? null;
            $item['payment_status'] = $d->payment_status;
            $item['bank_name'] = $d->bank_name;
            $item['no_rek'] = $d->no_rek;
            $item['an']     = $d->an;
            $item['to_bank']    = $d->to_bank;
            $item['file']       = asset($d->file);
            $item['method'] = $d->method;
            $list[]     = $item;
        }

        return response()->json([
            'payment' => $list,
            'message' => 'success'
        ]);
    }

    public function getElement($id)
    {
        $data = Transaction::findOrFail($id);
        return response()->json([
            'max_amount' => $data->due_total_po,
            'message' => 'success'
        ]);
    }

    public function domVariantItem($id)
    {
        $getData    = Variation::findOrFail($id);
        $getTax     = Taxrate::all();
        $unit       = $getData->unit ?? null;
        $unitPO = $getData->unitpo ?? null;
        $getUnit    = null;

        if ($unit != null) {
            $getUnit = $unit->unit_turunan;
        }

        $subtotal = $getData->purchase_price;
        if ($unitPO != null) {
            $valueUnit = $getData->unitpo->value ?? 1;
            $subtotal = $getData->purchase_price * $valueUnit;

            $listunit = array();
            foreach ($getUnit as $u) {
                if ($u->id != $getData->unit_purchase) {
                    $i['id']    = $u->id;
                    $i['name']  = $u->name;
                    $i['value'] = $u->value;
                    $listunit[] = $i;
                }
            }
        } else {
            $listunit = $getUnit;
        }

        $margin = $getData->margin_grocery;
        $price = $getData->grocery;
        if ($getData->margin_grocery == null || $getData->margin_grocery == 0) {
            $margin = $getData->margin;
            $price = $getData->selling_price;
        }

        return response()->json([
            'product' => [
                'name'      => $getData->product->name . ' - ' . $getData->name,
                'id'        => $getData->id,
                'pname'     => $getData->product->name,
                'pid'       => $getData->product->id,
                'margin'    => $getData->margin,
                'p_price'   => $getData->purchase_price,
                's_price'   => $getData->selling_price,
                'stock'     => $getData->stock_total,
                'g_price'   => $price,
                'unit_purchase' => $getData->unit_purchase,
                'subtotal'  => $subtotal,
                'g_margin'  => $margin
            ],
            'taxrate'       => $getTax,
            'unit'          => $unitPO,
            'unitdown'      => $listunit
        ]);
    }

    public function getTax($id)
    {
        $data = Taxrate::where("id", $id)->first();
        if ($data == null) {
            return 0;
        } else {
            return $data->taxrate;
        }
    }

    public function detail($id)
    {
        $status = [
            'received'      => __('purchase.received'),
            'ordered'       => __('purchase.ordered'),
            'pending'       => __('purchase.pending')
        ];

        $payment = [
            'due'   => __('general.po_due'),
            'paid'  => __('general.paid')
        ];

        $purchase = Transaction::findOrFail($id);
        $getDetail = Purchase::where('transaction_id', $id)->get();
        return view('admin.purchase.detail', ['page' => __('purchase.detail')], compact('getDetail', 'purchase', 'status', 'payment'));
    }

    public function printInvoice($id)
    {

        $status = [
            'received'      => __('purchase.received'),
            'ordered'       => __('purchase.ordered'),
            'pending'       => __('purchase.pending')
        ];

        $payment = [
            'due'   => __('general.po_due'),
            'paid'  => __('general.paid')
        ];
        $purchase = Transaction::findOrFail($id);
        $getDetail = Purchase::where('transaction_id', $id)->get();
        return view('admin.purchase.print_invoice', ['page' => __('purchase.detail')], compact('getDetail', 'purchase', 'status', 'payment'));
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status'        => 'required'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'error'
                ]);
            }
        }

        $data = Transaction::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();

        foreach ($data->purchase as $p) {
            $CheckSkus = Stock::where('product_id', $p->product_id)->where('variation_id', $p->variation_id)->where('store_id', Session::get('mystore'))->first();
            if ($CheckSkus == null) {
                $skus = new Stock();
                $skus->qty_available          = $p->quantity;
            } else {
                $skus = Stock::findOrFail($CheckSkus->id);
                $skus->qty_available          = $skus->qty_available +  $p->quantity;
            }
            $skus->product_id     = $p->product_id;
            $skus->variation_id   = $p->variation_id;
            $skus->store_id       = $data->store_id;
            $skus->save();
        }
        $data->save();
    }

    public function updatePayment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'payment_status'    => 'required'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'error'
                ]);
            }
        }

        $data = Transaction::findOrFail($request->id);
        $data->payment_status = $request->payment_status;
        $data->save();
    }

    public function report(Request $request)
    {

        if (!Auth::user()->can('Laporan Purchase')) {
            abort(403, 'Unauthorized action.');
        }

        $status = [
            'received'      => __('purchase.received'),
            'ordered'       => __('purchase.ordered'),
            'pending'       => __('purchase.pending')
        ];

        $payment = [
            'due'   => __('general.po_due'),
            'paid'  => __('general.paid')
        ];


        if ($request->ajax()) {
            $data = Transaction::where('type', 'purchase')->orderBy('id', 'desc')
                ->where(function ($query) use ($request) {
                    return $request->store ?  $query->where('store_id', $request->store) : '';
                })->where(function ($query) use ($request) {
                    return $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
                })->where(function ($query) use ($request) {
                    return $request->payment ? $query->where('payment_status', $request->payment) : '';
                })->where(function ($query) use ($request) {
                    return $request->start_date && $request->end_date ? $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
                })->where(function ($query) use ($request) {
                    return $request->status ?  $query->where('status', $request->status) : '';
                })->where(function ($query) {
                    return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
                })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        if (Auth::user()->can("Detail Purchase")) {
                            $html .= '<a class="dropdown-item" href="' . route('purchase.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';
                        }

                        if (Auth::user()->can("Print Purchase")) {
                            $html .= '<a class="dropdown-item" href="' . route('purchase.print', $row->id) . '"><i class="fa fa-print"></i> ' . __('general.print') . ' </a>';
                        }

                        if (Auth::user()->can("Produk Label")) {
                            $html .= '<a class="dropdown-item" href="' . route('barcode.purchase', $row->id) . '"><i class="fa fa-barcode"></i> ' . __('produk.print_label') . ' </a>';
                        }

                        if (Auth::user()->can("Tambah Return")) {
                            $returnqty = $row->purchase()->get()->sum('qty_return');
                            if ($row->qty_purchase > $returnqty) {
                                $html .= '<a class="dropdown-item" href="' . route('return.po', $row->id) . '"><i class="fa fa-repeat"></i> ' . __('purchase.return') . ' </a>';
                            }
                        }

                        if ($row->status != 'received') {
                            if (Auth::user()->can("Update Status Purchase")) {
                                $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="getstatusmodal(this.id)"><i class="fa fa-check-circle"></i> ' . __('general.change_status') . ' </a>';
                            }
                        }

                        if (count($row->payment) > 0) {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="showPayment_(this.id)"><i class="fa fa-money"></i> Lihat Pembayaran </a>';
                        }

                        if ($row->due_total_po != '0') {
                            if (Auth::user()->can("Update Status Purchase")) {
                                $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '"  onclick="getpaymentmodal_purchase(this.id)"><i class="fa fa-check-circle"></i> ' . __('general.add_payment') . ' </a>';
                            }
                        }

                        $html .= '</div></div></div>';

                        return $html;
                    }
                )->addColumn('identity', function ($row) {
                    return  $row->id;
                })->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at) . '<input type="hidden" id="idpo" value="' . $row->id . '">';
                })->addColumn('my_store', function ($row) {
                    return  $row->store->name ?? '';
                })->addColumn('my_supplier', function ($row) {
                    return  $row->supplier->name ?? '';
                })->addColumn('my_product', function ($row) {
                    return  count($row->purchase);
                })->addColumn('my_qty', function ($row) {
                    return  $row->qty_purchase;
                })->editColumn('final_total', function ($row) {
                    return  number_format($row->final_total);
                })->addColumn(
                    'my_status',
                    function ($row) use ($status) {
                        $html =  '<span class=" badge bg-primary text-white">' . $status[$row->status] . '</span>';
                        return $html;
                    }
                )->addColumn(
                    'my_return',
                    function ($row) {
                        $returned = 0;
                        $returnqty = $row->purchase()->get()->sum('qty_return');
                        if ($returnqty > 0) {
                            $returned  = $returnqty;
                        }
                        return $returned;
                    }
                )->addColumn(
                    'my_payment_status',
                    function ($row) {
                        $payment = [
                            'due'   => __('general.po_due'),
                            'paid'  => __('general.paid')
                        ];
                        $html =  '<span class=" badge bg-primary text-white">' . $payment[$row->payment_status] . '</span>';
                        return $html;
                    }
                )->addColumn('total_pay', function ($row) {
                    return $row->pay_total;
                })->addColumn('due_total', function ($row) {
                    return number_format($row->due_total_po ?? $row->final_total);
                })
                ->rawColumns(['action', 'identity', 'mydate', 'my_store', 'my_supplier', 'my_status', 'my_payment_status', 'total_pay', 'due_total', 'my_return', 'my_product', 'my_qty', 'final_total'])
                ->make(true);
        }

        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        $supplier = Supplier::all();

        return view('admin.reports.transaction.purchase', ['page' => __('sidebar.purchase_report')], compact(
            'store',
            'supplier',
            'status',
            'payment'
        ));
    }

    public function download(Request $request)
    {
        $getTransaction = Transaction::where('type', 'purchase')->orderBy('id', 'desc')
            ->where(function ($query) use ($request) {
                return $request->store ?  $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
            })->where(function ($query) use ($request) {
                return $request->payment ? $query->where('payment_status', $request->payment) : '';
            })->where(function ($query) use ($request) {
                return $request->start_date && $request->end_date ? $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
            })->where(function ($query) use ($request) {
                return $request->status ?  $query->where('status', $request->status) : '';
            })->where(function ($query) use ($request) {
                return $request->payment ? $query->where('payment_status', $request->payment) : '';
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            });

        $data = $getTransaction->get();

        $status = [
            'received'      => __('purchase.received'),
            'ordered'       => __('purchase.ordered'),
            'pending'       => __('purchase.pending')
        ];

        $payment = [
            'due'   => __('general.po_due'),
            'paid'  => __('general.paid')
        ];

        $jumlahTotal = 0;
        $jumlahHutang = 0;
        $jumlahTerbayar = 0;
        foreach ($data as $d) {
            $jumlahTotal += $d->final_total;
            $jumlahHutang += $d->due_total_po;
            $jumlahTerbayar += Helper::fresh_aprice($d->pay_total);
        }

        if ($request->excel == 'true') {
            return Excel::download(new PurchaseExportDefaulth($data, $status, $payment, $jumlahTotal, $jumlahHutang, $jumlahTerbayar), 'repots_purchase-' . $request->start_date . '.xlsx');
        } else {
            $pdf = PDF::loadView('admin.export.pdf.purchase', compact('data', 'status', 'payment', 'jumlahTotal', 'jumlahHutang', 'jumlahTerbayar'))->setPaper('a4', 'landscape');
            return $pdf->stream('purchase_reports-' . $request->start_date . '.pdf');
        }
    }

    public function claimExpire($id)
    {
        $getPO = Purchase::find($id);
        if ($getPO == null) {
            return redirect()->back();
        }

        $subtotal = ($getPO->qty_sold + $getPO->qty_adjusted) + ($getPO->qty_return + $getPO->qty_transfer) + $getPO->qty_expire;
        $change = $getPO->quantity - $subtotal;
        $getPO->qty_expire = $change;
        $getPO->save();

        $getStock = Stock::where("variation_id", $getPO->variation_id)->where("product_id", $getPO->product_id)->where("store_id", $getPO->store_id)->first();
        if ($getStock != null) {
            $getStock->qty_available = $getStock->qty_available - $change;
            $getStock->save();
        }

        return back()->with(['flash' => "Claim Expire Telah Dilakukan"]);
    }
}
