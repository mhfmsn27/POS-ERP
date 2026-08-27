<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account\Expense;
use App\Models\Hrm\Attendance;
use App\Models\Product\Product;
use App\Models\Product\Variation;
use App\Models\Timezone;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

class AdminController extends Controller
{

    public function index()
    {
        $data = [
            'total_purchase'    => Transaction::where('type', 'purchase')->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->sum('final_total'),
            'total_sell'        => Transaction::where('type', 'sell')->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->sum('final_total'),
            'total_due'         => Transaction::where('type', 'sell')->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->where('status', 'due')->sum("final_total"),
            'total_expense'           => Expense::where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->sum('amount'),
            'act_sell'          => Transaction::where('type', 'sell')->where("status", "final")->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->orderBy('id', 'desc')->limit(10)->get(),
            'act_purchase'      => Transaction::where('type', 'purchase')->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->orderBy('id', 'desc')->limit(10)->get(),
            'act_stransfer'     => Transaction::where('type', 'stock_transfer')->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->orderBy('id', 'desc')->limit(10)->get(),
            'act_sadjustment'   => Transaction::where('type', 'stock_adjustment')->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->orderBy('id', 'desc')->limit(10)->get(),
            'act_return'        => Transaction::where('type', 'purchase_return')->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->orderBy('id', 'desc')->limit(10)->get(),
            'act_returnsell'    => Transaction::where("type", "sales_return")->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->orderBy("id", "desc")->limit(10)->get(),
            'attendance'        => Attendance::where('date', date('Y-m-d'))->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->where('user_id', Auth()->user()->id)->first()
        ];

        $logs = Activity::where('store_id', my_store())->orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.index', ['page' => __('admin') . ' ' . __('dashboard')], compact('data', 'logs'));
    }

    public function incomeAndExpense()
    {
        $jumlah = DB::table("transactions as t")
            ->join('sells as s', 't.id', '=', 's.transaction_id')
            ->join('sell_purchases as sp', 's.id', '=', 'sp.sell_id')
            ->join('purchases as pp', 'sp.purchase_id', '=', 'pp.id')
            ->selectRaw("SUM((s.qty * s.unit_price) - (s.qty * pp.purchase_price)) AS jumlah")
            ->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('t.store_id', Auth::user()->store_id) : '';
            })
            ->get();
        $data['expense']    = Expense::selectRaw("sum(amount) as jumlah")->where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->get();
        $data['income']     = $jumlah;
        return response()->json($data);
    }

    public function transactionData()
    {
        $sell               = Transaction::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->where("type", "sell")->where("payment_status", "paid")->sum('final_total');
        $purchase           = Transaction::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->where("type", "purchase")->where("payment_status", "paid")->sum('final_total');
        $purchase_return    = Transaction::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->where("type", "purchase_return")->sum('final_total');
        $adjustment         = Transaction::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->where("type", "stock_adjustment")->sum('final_total');
        $transfer           = Transaction::where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->where("type", "stock_transfer")->sum('final_total');

        $total = $transfer + $sell + $purchase + $purchase_return + $adjustment;
        $data['sell']   = $sell > 0 ? $sell / $total * 100 : 0;
        $data['purchase']   = $purchase > 0 ? $purchase / $total * 100 : 0;
        $data['purchase_return']   = $purchase_return > 0 ? $purchase_return / $total * 100 : 0;
        $data['adjustment']   = $adjustment > 0 ? $adjustment / $total * 100 : 0;
        $data['transfer']   = $transfer > 0 ? $transfer / $total * 100 : 0;

        return response()->json($data);
    }

    public function sellmonth()
    {
        $data['selling'] = array();
        $selling = Transaction::selectRaw('LEFT(created_at,10) as date, sum(final_total) as total')->where('type', 'sell')->where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->whereYear('created_at', date('Y'))->groupBy('date')->limit(30)->get();
        foreach ($selling as $sell) {
            $list = [
                'date'  => Carbon::parse($sell->date, "UTC")->setTimezone(auth()->user()->timezone)->format("d, M Y"),
                'total' => $sell->total
            ];
            array_push($data['selling'], $list);
        }

        return response()->json($data);
    }

    public function topProduct()
    {
        $data = Sell::with(['variation', 'product'])
            ->selectRaw('sum(qty) as quantity, variation_id as variation, store_id as store')
            ->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })
            ->groupBy('variation', 'store')->limit(10)->get();

        $listdata = array();
        foreach ($data as $d) {

            $getVariant = Variation::where("id", $d->variation)->first();
            if ($getVariant != null) {
                $pname = $getVariant->product->name ?? '';
                if ($getVariant->name != 'no-name') {
                    $name = $pname . ' - ' . $getVariant->name;
                } else {
                    $name = $pname;
                }
                $list = [
                    'name'  => $name,
                    'selling'   => $d->quantity,
                    'color' => "#67b7dc",
                    'bullet' =>  asset($getVariant->gambar->path ?? '/uploads/image.jpg'),
                ];
                array_push($listdata, $list);
            }
        }
        return response()->json($listdata);
    }

    public function myProfile()
    {
        $timezone = Timezone::ZONETIME;
        return view('admin.profile', ['page' => 'Edit Akun Personal'], compact('timezone'));
    }

    public function changeProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'email' => 'required|unique:users,email,' . Auth::user()->id,
            'photo' => 'mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'error'
                ]);
            }
        }

        $data = User::findOrFail(Auth::user()->id);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->timezone = $request->timezone;
        $request->photo ? $data->photo = $this->uploadImage($request, 'photo', 'users') : null;
        return $this->saveData($data);
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'password'  => 'required',
            'confirm' => 'required',
        ]);

        if ($request->password != $request->confirm) {
            return back()->with(['gagal' => __('auth.password_must_same')]);
        }

        $data = User::findOrFail(Auth::user()->id);
        $data->password = Hash::make($request->password);
        return $this->saveData($data);
    }

    public function resetName()
    {
        $products = Product::all();
        foreach ($products as $p) {
            foreach ($p->variant as $v) {
                if ($v->name == $p->name) {
                    $v->name = '';
                    $v->save();
                }
            }
        }
    }
}
