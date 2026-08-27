<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use App\Models\Transaction\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::orderBy('name', 'asc')->get();
        return view('admin.customer.index', ['page' => __('sidebar.customer')], compact('data'));
    }

    public function create()
    {
        return view('admin.customer.create', ['page' => __('sidebar.add_customer')]);
    }

    public function update($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customer.update', ['page' => __('customer.update')], compact('customer'));
    }

    public function store(Request $request, $condition)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'error'
                ]);
            }
        }

        $condition == 'create' ? $data = new Customer() : $data = Customer::findOrFail($request->id);
        $data->name = $request->name;
        $data->code = $request->code;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $request->address ? $data->address = $request->address : null;
        $request->city ? $data->city = $request->city : null;
        $request->state ? $data->state = $request->state : null;
        return $this->saveData($data);
    }

    public function delete($id)
    {
        $data = Customer::findOrFail($id);
        return $this->deleteData($data, $id);
    }

    public function reports($id)
    {
        $data = [
            'total_transaction_sell'    => Transaction::where("customer_id", $id)->where("status", "final")->where("type", "sell")->count(),
            'total_transaction_return'  => Transaction::where("customer_id", $id)->where("status", "final")->where("type", "sales_return")->count(),
            'sell'                      => Transaction::where("customer_id", $id)->where("status", "final")->where("type", "sell")->sum("final_total"),
            'return'                    => Transaction::where("customer_id", $id)->where("status", "final")->where("type", "sales_return")->sum("final_total"),
            'sumbangsi_income'          => Transaction::where("customer_id", $id)->where("status", "final")->where("type", "sell")->get()->sum(function ($transaction) {
                return $transaction->profit;
            }),
            'diskon_diberikan'          => Transaction::where("customer_id", $id)->where("status", "final")->where("type", "sell")->get()->sum(function ($transaction) {
                $total = 0;
                if ($transaction->discount_type == 'percent') {
                    $total = $transaction->discount_amount / 100 * $transaction->sell()->sum("unit_price");
                } else {
                    $total = $transaction->discount_amount;
                }

                return $total;
            }),
            'id'                        => $id,
        ];

        return view('admin.customer.detail', ["page" => "Detail Laporan Pelanggan"], compact("data"));
    }
}
