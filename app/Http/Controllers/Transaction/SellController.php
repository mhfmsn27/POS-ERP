<?php

namespace App\Http\Controllers\Transaction;

use App\Exports\SellingExportDefaulth;
use App\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionSellRequest;
use App\Http\Resources\TransactionDetailResource;
use App\Models\Admin\Customer;
use App\Models\Admin\Store;
use App\Models\Crm\SalesCommission;
use App\Models\Crm\SalesCommissionAgent;
use App\Models\Hrm\Employee;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Observers\TransactionObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class SellController extends Controller
{


    /**
     * Handling Construct Process Insert Data
     * 
     */

    protected $transactionObserver;
    public function __construct(TransactionObserver $transactionObserver)
    {
        $this->transactionObserver   = $transactionObserver;
    }


    public function store(TransactionSellRequest $request)
    {

        if ($request->hold_transaction == true) {
            return $this->hold($request);
        } else {
            return $this->final($request);
        }
    }

    public function hold(Request $request)
    {
        try {

            DB::beginTransaction();

            $data                   = $this->transactionObserver->saveOrUpdate($request);
            $data->store_id         = Session::get('mystore');
            $data->type             = 'sell';
            $productDetails         = collect($request->details);

            $data->created_by       = Auth()->user()->id;
            $data->invoice_no       = rand();
            $data->ref_no           = rand();
            $data->transaction_date = date('Y-m-d H:i:s');
            $data->discount_type    = $request->discount_type;
            $data->customer_id      = $request->customer_id;
            $data->shipping_charges = $request->shipping;
            $data->other_charges    = $request->other_price;

            $data->total_before_tax = $productDetails->sum("subtotal");
            $data->tax_amount       = $request->tax;

            $data->discount_amount  = Helper::fresh_aprice($request->discount);
            $data->final_total      = Helper::fresh_aprice($request->fixtotal);
            $data->status           = 'hold';
            $data->save();

            $this->transactionObserver->saveProductDetail($data, $productDetails, 'hold');

            return response()->json([
                'message' => 'hold',
                'status'        => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'pesan' => $e->getMessage()]);
        }
    }

    public function final(Request $request)
    {
        try {

            DB::beginTransaction();

            $data                   = $this->transactionObserver->saveOrUpdate($request);
            $data->store_id         = Session::get('mystore');
            $data->type             = 'sell';
            $data->status           = 'final';
            $data->created_by       = Auth()->user()->id;
            $data->transaction_date = date('Y-m-d H:i:s');
            $data->customer_id      = $request->customer_id;
            $data->shipping_charges = Helper::fresh_aprice($request->shipping);
            $data->other_charges    = Helper::fresh_aprice($request->other_price);
            $data->payment_service  = Helper::fresh_aprice($request->payment_service);
            $data->discount_type    = $request->discount_type;
            $productDetails         = collect($request->details);
            $data->discount_amount  = Helper::fresh_aprice($request->discount);
            $data->tax_amount       = Helper::fresh_aprice($request->tax);

            if ($request->voucher_id) {
                $data->product_discount_id = $request->voucher_id;
            }

            $data->save();

            if ($request->voucher_id) {
                $this->transactionObserver->saveVoucher($data, $request);
            }


            $this->transactionObserver->saveProductDetail($data, $productDetails, 'final');


            $taxTotal               = Helper::fresh_aprice($request->tax) > 0 && $data->subtotal_sell > 0 ? Helper::fresh_aprice($request->tax) / 100 * $data->subtotal_sell : 0;
            $finalTotal             = ($data->subtotal_sell + $taxTotal + Helper::fresh_aprice($request->payment_service) + Helper::fresh_aprice($request->other_price) + Helper::fresh_aprice($request->shipping)) - Helper::fresh_aprice($request->discount);

            $change = 0;
            if (Helper::fresh_aprice($request->on_pay) >= $finalTotal || Helper::fresh_aprice($request->on_pay) == $finalTotal) {

                $pay_back = Helper::fresh_aprice($request->on_pay) - $finalTotal;
                $change = abs($pay_back);
                $paymentStatus = 'paid';
            } else {
                $paymentStatus = 'due';
            }



            $data->update([
                'total_before_tax'      => $data->subtotal_sell,
                'final_total'           => $finalTotal,
                'payment_status'        => $paymentStatus
            ]);

            if (Helper::fresh_aprice($request->on_pay) > 0) {
                $payment =  $this->transactionObserver->transactionPayment($data, $request);
            }

            $store = Store::findOrFail(Session::get('mystore'));
            if ($store->commission_system == 1) {
                $commission_agent = $this->commissionAgent($data->total_before_tax, $data, $request);

                if ($commission_agent != null) {
                    $transaction_update = Transaction::findOrFail($data->id);
                    $transaction_update->commission_contact_id = $commission_agent->commission_contact_id;
                    $transaction_update->commission_contact_type = $commission_agent->commission_contact_type;
                    $transaction_update->commission_contact_total = $commission_agent->commission_total;
                    $transaction_update->save();
                }
            }

            DB::commit();
            return response()->json([
                'status'        => true,
                'transaction'   => $data,
                'shipping'      => number_format($request->shipping),
                'other'         => number_format($request->other_price),
                'store'         => $data->store->name,
                'address'       => $data->store->address,
                'payment'       => $request->on_pay,
                'due'           => number_format($data->due_total),
                'change'        => number_format($change),
                'paymethod'     => !empty($payment->method) ? $payment->method : '-',
                'subtotal'      => number_format($data->total_before_tax),
                'sell'          => TransactionDetailResource::collection($data->sell),
                'voucher'       => $data->voucher,
                'message' => __('success')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'pesan' => $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile(),
            ]);
        }
    }

    public function commissionAgent($subtotal, $data, $request)
    {
        $store = Store::findOrFail(Session::get('mystore'));
        $commission_agent = null;
        if ($store->commission_type == 'none') {
            if (Auth::user()->commission_percentase > 0) {
                $commissionTotal = (Auth::user()->commission_percentase / 100) * $subtotal;

                if (Auth::user()->max_commission > 0) {
                    if ($commissionTotal > Auth::user()->max_commission) {
                        $commissionTotal = Auth::user()->max_commission;
                    }
                }

                $commission_agent = new SalesCommission();
                $commission_agent->transaction_id = $data->id;
                $commission_agent->commission_contact_id = Auth::user()->id;
                $commission_agent->commission_contact_type = 'none';
                $commission_agent->commission_percentase = Auth::user()->commission_percentase;
                $commission_agent->commission_total = $commissionTotal;
                $commission_agent->save();
            }
        } else {
            if ($request->agent_commission_id != null) {
                if ($store->commission_type == 'agent') {
                    $agentUser = SalesCommissionAgent::findOrFail($request->agent_commission_id);
                    if ($agentUser->commission_percentase > 0) {
                        $commissionTotal = ($agentUser->commission_percentase / 100) * $subtotal;

                        if ($agentUser->max_commission > 0) {
                            if ($commissionTotal > $agentUser->max_commission) {
                                $commissionTotal = $agentUser->max_commission;
                            }
                        }

                        $commission_agent = new SalesCommission();
                        $commission_agent->transaction_id = $data->id;
                        $commission_agent->commission_contact_id = $agentUser->id;
                        $commission_agent->commission_contact_type = 'agent';
                        $commission_agent->commission_percentase = $agentUser->commission_percentase;
                        $commission_agent->commission_total = $commissionTotal;
                        $commission_agent->save();
                    }
                } else if ($store->commission_type == 'user') {
                    $agentUser = User::findOrFail($request->agent_commission_id);
                    if ($agentUser->commission_percentase > 0) {
                        $commissionTotal = ($agentUser->commission_percentase / 100) * $subtotal;

                        if ($agentUser->max_commission > 0) {
                            if ($commissionTotal > $agentUser->max_commission) {
                                $commissionTotal = $agentUser->max_commission;
                            }
                        }

                        $commission_agent = new SalesCommission();
                        $commission_agent->transaction_id = $data->id;
                        $commission_agent->commission_contact_id = $agentUser->id;
                        $commission_agent->commission_contact_type = 'user';
                        $commission_agent->commission_percentase = $agentUser->commission_percentase;
                        $commission_agent->commission_total = $commissionTotal;
                        $commission_agent->save();
                    }
                } else if ($store->commission_type == 'employee') {
                    $agentUser = Employee::findOrFail($request->agent_commission_id);
                    if ($agentUser->commission_percentase > 0) {
                        $commissionTotal = ($agentUser->commission_percentase / 100) * $subtotal;

                        if ($agentUser->max_commission > 0) {
                            if ($commissionTotal > $agentUser->max_commission) {
                                $commissionTotal = $agentUser->max_commission;
                            }
                        }

                        $commission_agent = new SalesCommission();
                        $commission_agent->transaction_id = $data->id;
                        $commission_agent->commission_contact_id = $agentUser->id;
                        $commission_agent->commission_contact_type = 'employee';
                        $commission_agent->commission_percentase = $agentUser->commission_percentase;
                        $commission_agent->commission_total = $commissionTotal;
                        $commission_agent->save();
                    }
                }
            }
        }

        return $commission_agent;
    }

    public function report(Request $request)
    {
        if (!Auth::user()->can('Daftar Penjualan')) {
            abort(403, 'Unauthorized action.');
        }

        $user = User::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->get();

        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        $customer = Customer::all();

        $payment = [
            'paid'  => 'Terbayar',
            'due'   => 'Hutang',
        ];

        $status = [
            'due'   => __('general.sell_due'),
            "paid"  => __('general.paid'),
            'final' => __('general.paid')
        ];


        if ($request->ajax()) {
            $data = Transaction::where('type', 'sell')->where("status", "!=", "hold")
                ->where(function ($query) use ($request) {
                    return $request->store ? $query->where('store_id', $request->store) : '';
                })->where(function ($query) use ($request) {
                    return $request->customer ? $query->where('customer_id', $request->customer) : '';
                })->where(function ($query) use ($request) {
                    return $request->payment ?  $query->where('payment_status', $request->payment) : '';
                })->where(function ($query) use ($request) {
                    return $request->createdby ?  $query->where('created_by', $request->createdby) : '';
                })->where(function ($query) use ($request) {
                    if ($request->end_date && $request->start_date) {
                        return $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                    }
                    if ($request->date_now) {
                        return $query->whereDate('created_at', $request->date_now);
                    }
                })->where(function ($query) {
                    return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
                })
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        if (Auth::user()->can("Detail Penjualan")) {
                            $html .= '<a class="dropdown-item" href="' . route('sell.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';
                        }

                        if (Auth::user()->can("Print Penjualan")) {
                            $html .= '<a class="dropdown-item" href="' . route('sell.print', $row->id) . '"><i class="fa fa-print"></i> ' . __('general.print') . ' </a>';
                        }

                        if (Auth::user()->can("Return Penjualan")) {
                            $returnqty = $row->sell->sum('qty_return');
                            if ($row->qty_sell > $returnqty) {
                                $html .= '<a class="dropdown-item" href="' . route('returnsell.create', $row->id) . '"><i class="fa fa-repeat"></i> ' . __('sell.return_sell') . ' </a>';
                            }
                        }

                        if (count($row->payment) > 0) {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="showPayment_(this.id)"><i class="fa fa-money"></i> Lihat Pembayaran </a>';
                        }

                        if ($row->due_total != '0') {
                            if (Auth::user()->can("Tambah Pembayaran Penjualan")) {
                                $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '"  onclick="getpaymentmodal(this.id)"><i class="fa fa-money"></i> ' . __('general.add_payment') . ' </a>';
                            }
                        }

                        $html .= '</div></div></div>';
                        return $html;
                    }
                )->addColumn('mydate', function ($row) {
                    return  substr($row->created_at, 0, 10) . '<input type="hidden" id="idpo" value="' . $row->id . '">';
                })->addColumn('my_store', function ($row) {
                    return  $row->store->name ?? '';
                })->addColumn('my_cystomer', function ($row) {
                    return  $row->customer->name ?? '';
                })->addColumn(
                    'my_status',
                    function ($row) use ($status) {
                        $html =  '<span class=" badge bg-primary text-white">' . $status[$row->payment_status] . '</span>';
                        return $html;
                    }
                )->addColumn(
                    'my_sale',
                    function ($row) use ($status) {
                        return count($row->sell);
                    }
                )->addColumn(
                    'qty_sale',
                    function ($row) use ($status) {
                        $qtysell = $row->qty_sell;
                        return $qtysell;
                    }
                )->addColumn(
                    'qty_return',
                    function ($row) use ($status) {
                        $returnsell = 0;
                        $returnqty = $row->sell->sum('qty_return');
                        if ($returnqty > 0) {
                            $returnsell = $returnqty;
                        }
                        return $returnsell;
                    }
                )->editColumn('final_total', function ($row) {
                    return number_format($row->final_total);
                })->addColumn('due_total', function ($row) {
                    return number_format($row->due_total ?? $row->final_total);
                })->addColumn('total_pay', function ($row) {
                    return $row->pay_total;
                })->addColumn('profit', function ($row) {
                    return number_format($row->profit);
                })->addColumn('created_by', function ($row) {
                    return $row->createdby->name ?? '';
                })
                ->rawColumns(['action',  'mydate', 'my_store', 'my_cystomer', 'my_status', 'my_sale', 'qty_sale', 'qty_return', 'final_total', 'total_pay', 'due_total', 'profit', 'created_by'])
                ->make(true);
        }

        return view('admin.reports.transaction.sell', ['page' => __('sidebar.sell_report')], compact('store', 'customer', 'payment', 'user', 'status'));
    }

    public function getElement($id)
    {
        $data = Transaction::findOrFail($id);
        return response()->json([
            'max_amount' => $data->due_total,
            'message' => 'success'
        ]);
    }

    public function detail($id)
    {
        $data = Transaction::findOrFail($id);
        $status = [
            'due'   => __('general.sell_due'),
            "paid"  => __('general.paid'),
            'final' => __('general.paid')
        ];

        return view('admin.reports.transaction.sell_detail', ['page' => __('report.sell_detail')], compact('data', 'status'));
    }


    public function print($id)
    {
        $data = Transaction::findOrFail($id);
        $status = [
            'due'   => __('general.sell_due'),
            "paid"  => __('general.paid'),
            'final' => __('general.paid')
        ];

        return view('admin.reports.transaction.sell_print', ['page' => __('report.sell_detail')], compact('data', 'status'));
    }

    public function download(Request $request)
    {
        $getData = Transaction::where('type', 'sell')->where("status", "!=", "hold")
            ->where(function ($query) use ($request) {
                return $request->store ? $query->where('store_id', $request->store) : '';
            })->where(function ($query) use ($request) {
                return $request->customer ? $query->where('customer_id', $request->customer) : '';
            })->where(function ($query) use ($request) {
                return $request->payment ?  $query->where('payment_status', $request->payment) : '';
            })->where(function ($query) use ($request) {
                return $request->createdby ?  $query->where('created_by', $request->createdby) : '';
            })->where(function ($query) use ($request) {
                return $request->start_date && $request->end_date ? $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]) : '';
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })
            ->orderBy('id', 'desc');

        $our = $getData->get();

        $status = [
            'due'   => __('general.sell_due'),
            "paid"  => __('general.paid'),
            'final' => __('general.paid')
        ];


        $jumlahTotal = 0;
        $jumlahHutang = 0;
        $jumlahTerbayar = 0;
        $jumlahProfit = 0;
        foreach ($our as $d) {
            $jumlahProfit += $d->profit;
            $jumlahTotal += $d->final_total;
            $jumlahHutang += $d->due_total;
            $jumlahTerbayar += Helper::fresh_aprice($d->pay_total);
        }

        if ($request->excel == 'true') {
            return Excel::download(new SellingExportDefaulth($our, $jumlahTotal, $jumlahHutang, $jumlahTerbayar, $jumlahProfit, $status), 'laporan_penjualan-' . $request->start_date . '.xlsx');
        } else {
            $pdf = PDF::loadView('admin.export.pdf.selling', [
                'data' => $our, 'jumlahTotal' => $jumlahTotal, 'jumlahHutang' => $jumlahHutang, 'jumlahTerbayar' => $jumlahTerbayar, 'jumlahProfit' => $jumlahProfit, 'status' => $status
            ])->setPaper('a3', 'landscape');
            return $pdf->stream('laporan_penjualan-' . $request->start_date . '.pdf');
        }
    }
}
