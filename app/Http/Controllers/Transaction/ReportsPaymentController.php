<?php

namespace App\Http\Controllers\Transaction;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\Bank;
use App\Models\Admin\Store;
use App\Models\Transaction\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ReportsPaymentController extends Controller
{
    public function sales(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        if ($request->ajax()) {

            $data = TransactionPayment::with("transaction", "account")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->whereHas("transaction", function ($q) {
                $q->where('type', "sell");
            })->whereHas('transaction', function ($q) {
                Auth::user()->store_id != 0 ? $q->where('store_id', Auth::user()->store_id) : '';
            })->where("transaction_type", "transaction")->whereHas('transaction', function ($q) use ($request) {
                return $request->store ? $q->where('store_id', $request->store) : '';
            })->where(function ($q) use ($request) {
                return $request->method ? $q->where('method', $request->method) : '';
            })->where(function ($q) use ($request) {
                if ($request->account == 'ya') {
                    return $q->where('account_id', "!=", null);
                } else if ($request->account == 'no') {
                    return $q->where('account_id', null);
                }
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('detail', function ($row) {

                    $customer = $row->transaction->customer->name ?? '';
                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no;
                    $transactionID = $row->transaction_id;
                    $html = '<p><b><i>Penjualan </i></b><br>Pelanggan : ' . $customer . '<br>Invoice No : ' . $invoiceNo . ' <br> Nomor Ref : <a href="' . route('sell.detail', $transactionID) . '">' . $refNo . '</a> </p>';

                    return  $html;
                })
                ->editColumn("amount", function ($row) {
                    return  number_format($row->amount);
                })
                ->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at);
                })->addColumn('my_store', function ($row) {
                    return  $row->transaction->store->name ?? '';
                })->addColumn('method_pay', function ($row) {
                    return  $row->payment_methode;
                })->editColumn(
                    'created_by',
                    function ($row) {
                        return $row->user->name ?? '';
                    }
                )->addColumn(
                    'account_name',
                    function ($row) {
                        return $row->account->account->name ?? '';
                    }
                )->editColumn('note', function ($row) {
                    $html = '<p><i>' . $row->note . '</i></p>';
                    return $html;
                })->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="modalDetailPayment(' . $row->id . ')"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="paymentIntegrationAccount(' . $row->id . ')"><i class="fa fa-refresh"></i> Sinkronkan Pembayaran </a>';

                        $html .= '</div></div></div>';

                        return $html;
                    }
                )
                ->rawColumns(['action', 'detail',  'note', 'method_pay'])
                ->make(true);
        }

        return view("admin.reports.payment.sales", ["page" => "Laporan Uang Masuk Penjualan"], compact("store"));
    }

    public function detail($id)
    {
        $data = TransactionPayment::with("account")->findOrFail($id);
        $bank = Bank::where("bank_code", $data->bank_id)->first();
        $item['methode'] = $data->payment_methode;
        $item['method'] = $data->method;
        $item['no_rek'] = $data->no_rek;
        $item['an']     = $data->an;
        $item['account'] = $data->account->account_id ?? '';
        $item['account_id'] = $data->account_id;
        $item['id']     = $data->id;
        $bank == null ? $item['bank_name'] = $data->bank_id : $item['bank_name']    = $bank->bank_name;
        $item['card_transaction_number']    = $data->card_transaction_number;
        $item['card_number']                = $data->card_number;
        $item['card_type']                  = $data->card_type;
        $item['card_holder_name']           = $data->card_holder_name;
        $item['card_month']                 = $data->card_month;
        $item['card_year']                  = $data->card_year;
        $item['card_security']              = $data->card_security;

        // Note
        $item['note']                       = $data->note;

        return response()->json($item);
    }

    public function integratePaymentLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id'        => 'required',
            'account_id'        => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'error'
                ]);
            }
        }

        $data = TransactionPayment::findOrFail($request->payment_id);
        $debt = "credit";



        if ($data->transaction_type == 'transaction') {
            $transactionType = $data->transaction->type ?? '';
            if ($transactionType == 'sell' || $transactionType == 'purchase_return') {
                $debt = "debit";
            }
        }

        if ($request->a_transaction) {
            $accountTransaction = AccountTransaction::findOrFail($request->a_transaction);
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->created_by = Auth::user()->id;
            $accountTransaction->type = 'credit';
            $accountTransaction->amount = $data->amount;
            $accountTransaction->operation_date = $data->created_at;
            $accountTransaction->sub_type = 'expense';
            $accountTransaction->transaction_id = $data->transaction_id;
            $accountTransaction->transaction_payment_id = $data->id;
            $accountTransaction->save();

            $data->account_id = $accountTransaction->id;
            $data->save();
        } else {
            Helper::createAccount($debt, $request, $data);
        }
    }

    public function purchase(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        if ($request->ajax()) {

            $data = TransactionPayment::with("transaction", "account")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->whereHas("transaction", function ($q) {
                $q->where('type', "purchase");
            })->whereHas('transaction', function ($q) {
                Auth::user()->store_id != 0 ? $q->where('store_id', Auth::user()->store_id) : '';
            })->where("transaction_type", "transaction")->whereHas('transaction', function ($q) use ($request) {
                return $request->store ? $q->where('store_id', $request->store) : '';
            })->where(function ($q) use ($request) {
                return $request->method ? $q->where('method', $request->method) : '';
            })->where(function ($q) use ($request) {
                if ($request->account == 'ya') {
                    return $q->where('account_id', "!=", null);
                } else if ($request->account == 'no') {
                    return $q->where('account_id', null);
                }
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('detail', function ($row) {

                    $customer = $row->transaction->customer->name ?? '';
                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no;
                    $transactionID = $row->transaction_id;
                    $html = '<p><b><i>Return Penjualan </i></b><br>Pelanggan : ' . $customer . '<br>Invoice No : ' . $invoiceNo . ' <br> Nomor Ref : <a href="' . route('returnsell.detail', $transactionID) . '">' . $refNo . '</a> </p>';

                    return  $html;
                })
                ->editColumn("amount", function ($row) {
                    return  number_format($row->amount);
                })
                ->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at);
                })->addColumn('my_store', function ($row) {
                    return  $row->transaction->store->name ?? '';
                })->addColumn('method_pay', function ($row) {
                    return  $row->payment_methode;
                })->editColumn(
                    'created_by',
                    function ($row) {
                        return $row->user->name ?? '';
                    }
                )->addColumn(
                    'account_name',
                    function ($row) {
                        return $row->account->account->name ?? '';
                    }
                )->editColumn('note', function ($row) {
                    $html = '<p><i>' . $row->note . '</i></p>';
                    return $html;
                })->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="modalDetailPayment(' . $row->id . ')"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="paymentIntegrationAccount(' . $row->id . ')"><i class="fa fa-refresh"></i> Sinkronkan Pembayaran </a>';

                        $html .= '</div></div></div>';

                        return $html;
                    }
                )
                ->rawColumns(['action', 'detail',  'note', 'method_pay'])
                ->make(true);
        }

        return view("admin.reports.payment.purchase", ["page" => "Laporan Pembayaran Pembelian (PO)"], compact("store"));
    }

    public function return_sell(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        if ($request->ajax()) {

            $data = TransactionPayment::with("transaction", "account")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->whereHas("transaction", function ($q) {
                $q->where('type', "sales_return");
            })->whereHas('transaction', function ($q) {
                Auth::user()->store_id != 0 ? $q->where('store_id', Auth::user()->store_id) : '';
            })->where("transaction_type", "transaction")->whereHas('transaction', function ($q) use ($request) {
                return $request->store ? $q->where('store_id', $request->store) : '';
            })->where(function ($q) use ($request) {
                return $request->method ? $q->where('method', $request->method) : '';
            })->where(function ($q) use ($request) {
                if ($request->account == 'ya') {
                    return $q->where('account_id', "!=", null);
                } else if ($request->account == 'no') {
                    return $q->where('account_id', null);
                }
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('detail', function ($row) {

                    $customer = $row->transaction->customer->name ?? '';
                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no;
                    $transactionID = $row->transaction_id;
                    $html = '<p><b><i>Return Penjualan </i></b><br>Pelanggan : ' . $customer . '<br>Invoice No : ' . $invoiceNo . ' <br> Nomor Ref : <a href="' . route('returnsell.detail', $transactionID) . '">' . $refNo . '</a> </p>';

                    return  $html;
                })
                ->editColumn("amount", function ($row) {
                    return  number_format($row->amount);
                })
                ->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at);
                })->addColumn('my_store', function ($row) {
                    return  $row->transaction->store->name ?? '';
                })->addColumn('method_pay', function ($row) {
                    return  $row->payment_methode;
                })->editColumn(
                    'created_by',
                    function ($row) {
                        return $row->user->name ?? '';
                    }
                )->addColumn(
                    'account_name',
                    function ($row) {
                        return $row->account->account->name ?? '';
                    }
                )->editColumn('note', function ($row) {
                    $html = '<p><i>' . $row->note . '</i></p>';
                    return $html;
                })->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="modalDetailPayment(' . $row->id . ')"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="paymentIntegrationAccount(' . $row->id . ')"><i class="fa fa-refresh"></i> Sinkronkan Pembayaran </a>';

                        $html .= '</div></div></div>';

                        return $html;
                    }
                )
                ->rawColumns(['action', 'detail',  'note', 'method_pay'])
                ->make(true);
        }

        return view("admin.reports.payment.return_sales", ["page" => "Laporan Pembayaran Return Pelanggan"], compact("store"));
    }

    public function return_po(Request $request)
    {
        $store = Store::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('id', Auth::user()->store_id) : '';
        })->get();

        if ($request->ajax()) {

            $data = TransactionPayment::with("transaction", "account")->where(function ($q) use ($request) {
                if ($request->end_date && $request->start_date) {
                    return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
                if ($request->date_now) {
                    return $q->whereDate('created_at', $request->date_now);
                }
            })->whereHas("transaction", function ($q) {
                $q->where('type', "purchase_return");
            })->whereHas('transaction', function ($q) {
                Auth::user()->store_id != 0 ? $q->where('store_id', Auth::user()->store_id) : '';
            })->where("transaction_type", "transaction")->whereHas('transaction', function ($q) use ($request) {
                return $request->store ? $q->where('store_id', $request->store) : '';
            })->where(function ($q) use ($request) {
                return $request->method ? $q->where('method', $request->method) : '';
            })->where(function ($q) use ($request) {
                if ($request->account == 'ya') {
                    return $q->where('account_id', "!=", null);
                } else if ($request->account == 'no') {
                    return $q->where('account_id', null);
                }
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('detail', function ($row) {

                    $customer = $row->transaction->customer->name ?? '';
                    $invoiceNo = $row->transaction->invoice_no;
                    $refNo = $row->transaction->ref_no;
                    $transactionID = $row->transaction_id;
                    $html = '<p><b><i>Return Penjualan </i></b><br>Pelanggan : ' . $customer . '<br>Invoice No : ' . $invoiceNo . ' <br> Nomor Ref : <a href="' . route('returnsell.detail', $transactionID) . '">' . $refNo . '</a> </p>';

                    return  $html;
                })
                ->editColumn("amount", function ($row) {
                    return  number_format($row->amount);
                })
                ->addColumn('mydate', function ($row) {
                    return  my_date($row->created_at);
                })->addColumn('my_store', function ($row) {
                    return  $row->transaction->store->name ?? '';
                })->addColumn('method_pay', function ($row) {
                    return  $row->payment_methode;
                })->editColumn(
                    'created_by',
                    function ($row) {
                        return $row->user->name ?? '';
                    }
                )->addColumn(
                    'account_name',
                    function ($row) {
                        return $row->account->account->name ?? '';
                    }
                )->editColumn('note', function ($row) {
                    $html = '<p><i>' . $row->note . '</i></p>';
                    return $html;
                })->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="modalDetailPayment(' . $row->id . ')"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';

                        $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="paymentIntegrationAccount(' . $row->id . ')"><i class="fa fa-refresh"></i> Sinkronkan Pembayaran </a>';

                        $html .= '</div></div></div>';

                        return $html;
                    }
                )
                ->rawColumns(['action', 'detail',  'note', 'method_pay'])
                ->make(true);
        }

        return view("admin.reports.payment.return_po", ["page" => "Laporan Pembayaran Return Pembelian (PO)"], compact("store"));
    }
}
