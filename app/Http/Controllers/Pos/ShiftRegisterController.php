<?php

namespace App\Http\Controllers\Pos;

use App\Exports\ShiftReports;
use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Models\Product\Variation;
use App\Models\Transaction\Sell;
use App\Models\Transaction\ShiftRegister;
use App\Models\Transaction\ShiftRegisterTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class ShiftRegisterController extends Controller
{
    public function today()
    {
        $store = Store::findOrFail(Session::get('mystore'));

        if ($store->shift_register != 'active') {
            return redirect()->route('index');
        }

        $getShift = ShiftRegister::whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->whereDay("created_at", date('d'))
            ->where("status", "open")
            ->where("store_id", Session::get('mystore'))
            ->first();

        if ($getShift == null) {
            return redirect()->route('pos.register')->with(['gagal' => "Silahkan Melakukan Open Shift Register Terlebih dahulu"]);
        }

        return view('pos.register_today', ["page" => "Rangkuman Transaksi Shift Register"], compact('getShift'));
    }

    public function getTransaction()
    {
        $getShift = ShiftRegister::whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->whereDay("created_at", date('d'))
            ->where("status", "open")
            ->where("store_id", Session::get('mystore'))
            ->first();

        $data['cash']   = ShiftRegisterTransaction::selectRaw("sum(amount) as total")
            ->where("transaction_type", "opening")
            ->where("shift_register_id", $getShift->id)
            ->get();

        $data['sell']   = ShiftRegisterTransaction::selectRaw("sum(amount) as total")
            ->where("transaction_type", "sell")
            ->where("shift_register_id", $getShift->id)
            ->get();

        $data['return'] = ShiftRegisterTransaction::selectRaw("sum(amount) as total")
            ->where("transaction_type", "refund")
            ->where("shift_register_id", $getShift->id)
            ->get();

        $data['expense']   = ShiftRegisterTransaction::selectRaw("sum(amount) as total")
            ->where("transaction_type", "expense")
            ->where("shift_register_id", $getShift->id)
            ->get();

        return response()->json($data);
    }

    public function getPayment()
    {
        $getShift = ShiftRegister::whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->whereDay("created_at", date('d'))
            ->where("status", "open")
            ->where("store_id", Session::get('mystore'))
            ->first();

        $data['cash']   = ShiftRegisterTransaction::selectRaw("sum(amount) as total")
            ->where("pay_method", "cash")
            ->where("shift_register_id", $getShift->id)
            ->get();

        $data['bank']   = ShiftRegisterTransaction::selectRaw("sum(amount) as total")
            ->where("pay_method", "bank")
            ->where("shift_register_id", $getShift->id)
            ->get();

        $data['other']   = ShiftRegisterTransaction::selectRaw("sum(amount) as total")
            ->where("pay_method", "other")
            ->where("shift_register_id", $getShift->id)
            ->get();

        return response()->json($data);
    }

    public function topProduct()
    {
        $data = Sell::with(['variation', 'product'])
            ->selectRaw('sum(qty) as quantity, variation_id as variation, store_id as store')
            ->whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->whereDay("created_at", date('d'))
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

    public function closeRegister()
    {
        $getShift = ShiftRegister::whereYear("created_at", date('Y'))
            ->whereMonth("created_at", date('m'))
            ->whereDay("created_at", date('d'))
            ->where("status", "open")
            ->where("store_id", Session::get('mystore'))
            ->first();


        if ($getShift == null) {
            return redirect()->route('pos.register')->with(['gagal' => "Silahkan Melakukan Open Shift Register Terlebih dahulu"]);
        }

        $getShift->close_amount = $getShift->cash_in_hand;
        $getShift->status = 'close';
        $getShift->closed_at = date("Y-m-d H:i:s");

        $other = $getShift->sell_bank_transaction + $getShift->sell_other_transaction;
        $getShift->other_amount = $other;
        return $this->saveData($getShift);
    }

    public function report(Request $request)
    {
        $user = User::all();

        $store = Store::where(function ($query) {
            if (Auth::user()->store_id != 0) {
                return $query->where('id', Auth::user()->store_id);
            }
        })->get();

        if ($request->ajax()) {
            $data = ShiftRegister::where(function ($query) use ($request) {
                if ($request->store) {
                    return $query->where('store_id', $request->store);
                }
            })->where(function ($query) use ($request) {
                if ($request->user) {
                    return $query->where('user_id', $request->user);
                }
            })->where(function ($query) use ($request) {
                if ($request->start && $request->end) {
                    return $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
                }
            })->where(function ($query) {
                return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
            })->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group mb-1"><div class="dropdown"><button class="btn btn-primary btn-sm dropdown-toggle me-1" type="button" id="dropdownMenuButtonIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="bi bi-error-circle me-50"></i> Action </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButtonIcon" style="margin: 0px; z-index:1000">';
                        $html .= '<a class="dropdown-item" href="' . route('shift.detail', $row->id) . '"><i class="fa fa-eye"></i> ' . __('general.detail') . ' </a>';
                        $html .= '<a class="dropdown-item" href="' . route('shift.print', $row->id) . '"><i class="fa fa-print"></i> ' . __('general.print') . ' </a>';

                        if ($row->status == 'open') {
                            $html .= '<a class="dropdown-item" href="javascript:void(0);" onclick="changeStatus(' . $row->id . ')"  ><i class="fa fa-check-circle"></i>Tutup Shift</a>';
                        }

                        $html .= '</div></div></div>';
                        return $html;
                    }
                )->addColumn('mydate', function ($row) {
                    return  dt($row->created_at);
                })->addColumn('my_store', function ($row) {
                    return  $row->store->name ?? '';
                })->addColumn('opening', function ($row) {
                    return  shiftTime($row->created_at);
                })->addColumn(
                    'closed',
                    function ($row) {
                        $close = '';
                        if ($row->closed_at != null) {
                            $close = substr($row->closed_at, 11, 5);
                        }
                        return $close;
                    }
                )->editColumn(
                    'open_amount',
                    function ($row) {
                        return number_format($row->open_amount);
                    }
                )->editColumn(
                    'close_amount',
                    function ($row) {
                        return   number_format($row->close_amount);
                    }
                )->editColumn(
                    'other_amount',
                    function ($row) {
                        return number_format($row->other_amount);
                    }
                )->addColumn('my_status', function ($row) {
                    $html = '';
                    if ($row->status == 'close') {
                        $html = '<span class="badge bg-danger text-white">Sudah Ditutup</span>';
                    } else {
                        $html = '  <span class="badge bg-primary text-white">Masih Dibuka</span>';
                    }
                    return $html;
                })->addColumn('my_transaction', function ($row) {
                    return count($row->transactionshift);
                })
                ->rawColumns(['action',  'mydate', 'my_store', 'opening', 'closed', 'open_amount', 'open_amount', 'close_amount', 'other_amount', 'my_status', 'my_transaction'])
                ->make(true);
        }
        return view('admin.reports.transaction.shift', ["page" => "Laporan Shift Register"], compact('store', 'user'));
    }

    public function close(Request $request, $id, \App\Services\Pos\ShiftRegisterService $shiftService)
    {
        $getShift = ShiftRegister::findOrFail($id);
        $physicalCount = $request->filled('physical_cash_count')
            ? (float)$request->physical_cash_count
            : (float)$getShift->cash_in_hand;

        $notes = $request->input('closing_notes', null);
        $result = $shiftService->closeShiftWithAudit($getShift->id, $physicalCount, $notes, auth()->id());

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->back()->with(['sukses' => $result['message']]);
    }

    public function zReport($id, \App\Services\Pos\ShiftRegisterService $shiftService)
    {
        $report = $shiftService->generateZReport((int)$id);
        return response()->json($report);
    }

    public function detail($id)
    {
        $data = ShiftRegister::findOrFail($id);
        return view('admin.reports.transaction.shift_detail', ["page" => "Detail Laporan Shift Register"], compact('data'));
    }

    public function print($id)
    {
        $data = ShiftRegister::findOrFail($id);
        return view('admin.reports.transaction.shift_print', ["page" => "Print Laporan Shift Register"], compact('data'));
    }

    public function download(Request $request)
    {
        $data = ShiftRegister::where(function ($query) use ($request) {
            if ($request->store) {
                return $query->where('store_id', $request->store);
            }
        })->where(function ($query) use ($request) {
            if ($request->user) {
                return $query->where('user_id', $request->user);
            }
        })->where(function ($query) use ($request) {
            if ($request->start && $request->end) {
                return $query->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            }
        })->where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->orderBy("id", "desc")->get();

        $date = $request->start_date ?? 'all';
        if ($request->excel == 'true') {
            return Excel::download(new ShiftReports($data), 'shift_reports-' . $date . '.xlsx');
        } else {
            $pdf = PDF::loadView('admin.export.pdf.shift', compact('data'))->setPaper('a4', 'landscape');
            return $pdf->stream('shift_reports-' . $date . '.pdf');
        }
    }
}
