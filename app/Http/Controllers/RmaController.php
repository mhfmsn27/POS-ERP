<?php

namespace App\Http\Controllers;

use App\Models\Admin\Store;
use App\Models\Rma\RmaTransaction;
use App\Observers\Rma\RmaObserver;
use Illuminate\Http\Request;

class RmaController extends Controller
{
    protected $rmaObserver;

    public function __construct(RmaObserver $rmaObserver)
    {
        $this->rmaObserver      = $rmaObserver;
    }

    public function index()
    {
        return view('rma.index', ['page'     => 'Check Rma']);
    }

    public function check(Request $request)
    {

        $datas          = explode("/", $request->referensi);
        $stores         = Store::withoutGlobalScopes()->where("id", $datas[1])->first(['id']);

        if (!$stores) {
            return redirect()->back()->with(['gagal'    => 'Maaf, Toko atau Perusahaan dari sumber referensi tidak ditemukan']);
        }

        $transactions   = RmaTransaction::withoutGlobalScopes()->where("store_id", $stores->id)->where("ref_no", $request->referensi)->first(['ref_no']);

        if (!$transactions) {
            return redirect()->back()->with(['gagal'    => 'Maaf, Transaksi dengan nomor referensi ' . $request->referensi . ', Tidak dapat kami temukan']);
        }

        return redirect()->route('detail.rma', strtolower(preg_replace("/[^0-9a-zA-Z]/", "-", $transactions->ref_no)));
    }

    public function detail(String $referensi)
    {
        $newRef         = strtolower(preg_replace("/[^0-9a-zA-Z]/", "/", $referensi));
        $transaction    = RmaTransaction::withoutGlobalScopes()->where("ref_no",$newRef )->first();

        if (!$transaction) {
            return redirect()->route('rma')->with(['gagal'    => 'Maaf, Transaksi dengan nomor referensi ' . $newRef . ', Tidak dapat kami temukan']);
        }

        return view('rma.detail', ['page'    => 'Detail Rma ' . $transaction->ref_no], compact('transaction'));
    }
}
