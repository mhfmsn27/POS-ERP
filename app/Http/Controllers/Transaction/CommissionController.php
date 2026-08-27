<?php

namespace App\Http\Controllers\Transaction;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Account\Expense;
use App\Models\Admin\Store;
use App\Models\Crm\SalesCommission;
use App\Models\Crm\SalesCommissionAgent;
use App\Models\Hrm\Employee;
use App\Models\Transaction\TransactionPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $user = User::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->get(["id", "name"]);

        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get(["id", "name"]);

        $employee = Employee::where(function ($q) {
            if (Auth::user()->store_id != 0) {
                return $q->whereHas('user', function ($query) {
                    $query->where('store_id', Auth::user()->store_id);
                });
            }
        })->get();

        $agent = SalesCommissionAgent::get(['id', "name"]);

        if ($request->ajax()) {
            $data = SalesCommission::with('transaction')
                ->whereHas('transaction', function ($q) use ($request) {
                    return $request->store ? $q->where('store_id', $request->store) : '';
                })->where(function ($query) use ($request) {
                    return $request->type ? $query->where('commission_contact_type', $request->type) : '';
                })->where(function ($query) use ($request) {
                    return $request->status ? $query->where('status', $request->status) : '';
                })->where(function ($query) use ($request) {
                    if ($request->employee) {
                        return $query->where('commission_contact_id', $request->employee);
                    }
                    if ($request->user) {
                        return $query->where('commission_contact_id', $request->user);
                    }

                    if ($request->agent) {
                        return $query->where('commission_contact_id', $request->agent);
                    }
                })->where(function ($query) use ($request) {
                    if ($request->end_date && $request->start_date) {
                        return $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                    }
                    if ($request->date_now) {
                        return $query->whereDate('created_at', $request->date_now);
                    }
                })->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';
                        $html .= '<a class="dropdown-item" href="' . route('commission.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';

                        if ($row->status == 'due') {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '"  onclick="getpaymentmodal(this.id)"><i class="fa fa-money"></i> ' . __('general.add_payment') . ' </a>';
                        }

                        if (count($row->expense) > 0) {
                            $html .= '<a class="dropdown-item" href="javascript:void(0)" id="' . $row->id . '" onclick="showPaymentCommission(this.id)"><i class="fa fa-money"></i> Lihat Pembayaran </a>';
                        }
 

                        $html .= '</div></div></div>';
                        return $html;
                    }
                )->addColumn('date', function ($row) {
                    return  substr($row->created_at, 0, 10) . '<input type="hidden" id="idpo" value="' . $row->id . '">';
                })->addColumn('no_ref', function ($row) {
                    return  "<a target='blank_' href='" . route('sell.detail', $row->transaction_id) . "'>" . $row->transaction->ref_no . "</a>";
                })->addColumn('store', function ($row) {
                    return  $row->transaction->store->name ?? '';
                })->addColumn('agent_n', function ($row) {
                    return  $row->agent_name;
                })->addColumn('agent_t', function ($row) {
                    return  $row->type_name;
                })->editColumn('commission_total_return', function ($row) {
                    return number_format($row->commission_total_return);
                })->editColumn('commission_percentase', function ($row) {
                    return number_format($row->commission_percentase) . '%';
                })->editColumn('commission_total', function ($row) {
                    return number_format($row->commission_total);
                })->addColumn(
                    'payment_status',
                    function ($row) {
                        $html =  '<span class=" badge bg-primary text-white">' . $row->status_name . '</span>';
                        return $html;
                    }
                )->editColumn(
                    'commission_total',
                    function ($row) {
                        return number_format($row->commission_total);
                    }
                )
                ->rawColumns(['action',  'date', 'store', 'agent_n', 'payment_status', 'no_ref'])
                ->make(true);
        }

        return view('admin.reports.transaction.commission', ['page' => "Laporan Komisi"], compact('store', 'employee', 'agent', 'user'));
    }

    public function commissionPayment(Request $request)
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

        $getCommission = SalesCommission::findOrFail($request->transaction_id);

        if ($getCommission->due_total == 0) {
            return response()->json([
                'errors' => "Tidak Ada Tunggakan Untuk Transaksi ini",
                'message' => 'nothing'
            ]);
        }

        if ($getCommission->due_total >= Helper::fresh_aprice($request->payment_amount)) {
            $pay    = Helper::fresh_aprice($request->payment_amount);
        } else {
            $pay    = $getCommission->due_total;
        }

        if ($getCommission->due_total == $pay) {
            $getCommission->status = 'pay';
            $getCommission->save();
        }


        $payment = new Expense();
        $payment->sales_commission_id    = $request->transaction_id;

        $ExpenseCount = Expense::whereDate("created_at", date("Y-m-d"))->count() + 1;
        $invoiceNumber =  sprintf("%05d", $ExpenseCount);
        $refNo = Helper::transactionKey('EP', $invoiceNumber);

        $payment->ref_no = $refNo;
        $payment->store_id = Session::get('mystore');

        $payment->name = "Komisi Penjualan " . number_format($getCommission->commission_percentase) . "% Untuk " . $getCommission->agent_name;
        $payment->refund = 'no';
        $payment->amount = Helper::fresh_aprice($request->payment_amount);
        $payment->payment_status = 'paid';
        $payment->type = 'commission';

        if ($getCommission->commission_contact_type == 'none' || $getCommission->commission_contact_type == 'user') {
            $payment->contact_type = 'user';
            $payment->contact_id = $getCommission->commission_contact_id;
        } else if ($getCommission->commission_contact_type == 'employee') {
            $payment->contact_type = 'user';
            $payment->contact_id = $getCommission->pegawai->user_id ?? '';
        } else {
            $payment->contact_type = 'agent';
            $payment->contact_id = $getCommission->commission_contact_id;
        }

        $payment->save();

        $transactionPayment = new TransactionPayment();
        $transactionPayment->amount            = $pay;
        $transactionPayment->created_by        = Auth::user()->id;
        $transactionPayment->transaction_id    = $payment->id;
        $transactionPayment->method            = $request->payment_method;
        $transactionPayment->transaction_type  = 'expense';
        $request->payment_note ? $transactionPayment->note = $request->payment_note : null;
        $request->account_id ? $transactionPayment->account_id = $request->account_id : null;
        if ($request->payment_method == 'bank_transfer') {
            $request->no_rek ? $transactionPayment->no_rek = $request->no_rek : null;
            $request->an ? $transactionPayment->an = $request->an : null;
            $request->bank_id ? $transactionPayment->bank_id = $request->bank_id : null;
        } else if ($request->payment_method == 'card') {
            $request->card_number ? $transactionPayment->card_number = $request->card_number : null;
            $request->card_holder_name ? $transactionPayment->card_holder_name = $request->card_holder_name : null;
            $request->card_transaction_number ? $transactionPayment->card_transaction_number = $request->card_transaction_number : null;
            $request->card_type ? $transactionPayment->card_type = $request->card_type : null;
            $request->card_month ? $transactionPayment->card_month = $request->card_month : null;
            $request->card_year ? $transactionPayment->card_year = $request->card_year : null;
            $request->card_security ? $transactionPayment->card_security = $request->card_security : null;
        }

        $transactionPayment->save();

        if ($request->account_id) {
            if ($request->payment_amount > 0) {
                Helper::createAccount("credit", $request, $transactionPayment);
            }
        }
    }

    public function getElement($id)
    {
        $data = SalesCommission::findOrFail($id);
        return response()->json([
            'max_amount' => $data->due_total,
            'message' => 'success'
        ]);
    }

    public function getPayment($id)
    {
        $data = Expense::where("type", "commission")->where("sales_commission_id", $id)->get();

        $list = array();
        foreach ($data as $d) {
            $item['date']   = substr($d->created_at, 0, 10);
            $item['amount'] = number_format($d->amount);
            $item['method'] = $d->payment->payment_methode ?? ''; 
            $item['account'] = $d->payment->account->account->name ?? null;
            $list[]     = $item;
        }

        return response()->json([
            'payment' => $list,
            'message' => 'success'
        ]);
    }

    public function detail($id)
    {
        $data = SalesCommission::findOrFail($id);
        return view('admin.reports.transaction.commission_detail', ['page' => "Laporan Detail Komisi"], compact('data'));
    }
}
