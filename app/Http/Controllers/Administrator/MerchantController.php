<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin\Merchant;
use App\Models\User;
use App\Observers\Saas\MerchantObserver;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    protected $merchantObserver;

    public function __construct(MerchantObserver $merchantObserver)
    {
        $this->merchantObserver     = $merchantObserver;
    }

    public function index(Request $request)
    {
        $merchants  = $this->merchantObserver->getData($request)->paginate(40);

        $pagination       = array(
            'current_page'      => $merchants->currentPage(),
            'to_page'           => $merchants->lastPage(),
            'per_page'          => $merchants->perPage(),
            'first_item'        => $merchants->firstItem(),
            'last_item'         => $merchants->lastItem(),
            'links'             => $merchants->linkCollection()->toArray()
        );

        return view('super.merchant.index', ['page' => 'Daftar Merchant'], compact('merchants', 'pagination'));
    }

    public function detail(Merchant $merchant)
    {
        return view('super.merchant.detail', ['page' => 'Detail Merchant'], compact('merchant'));
    }

    public function activationUser(User $user)
    {

        $user->update([
            'status'    => $user->status == 'active' ? 'no' : 'active'
        ]);

        return redirect()->back()->with(['flash' => 'Berhasil memperbaharui data']);
    }
}
