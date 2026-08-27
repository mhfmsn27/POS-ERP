<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Courier;
use App\Observers\Master\CourierObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourierController extends Controller
{
    protected $courierObserver;

    public function __construct(CourierObserver $courierObserver)
    {
        $this->courierObserver      = $courierObserver;
    }

    public function index(Request $request)
    {
        abort_if(Gate::denies('expedition_viw'), 403);
        $couriers       = $this->courierObserver->getData($request)->get();
        return view('admin.company.courier.index', ['page' => 'Daftar Kurir'], compact('couriers'));
    }

    public function search(Request $request)
    {
        $couriers       = $this->courierObserver->getData($request)->get(['id', 'name']);
        return response()->json([
            'couriers'    => $couriers,
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('expedition_create'), 403);
        return view('admin.company.courier.create', ['page' => 'Tambah data kurir']);
    }

    public function update(Courier $courier)
    {
        abort_if(Gate::denies('expedition_update'), 403);
        return view('admin.company.courier.update', ['page' => 'Edit Data Kurir'], compact('courier'));
    }

    public function store(Request $request)
    {

        abort_if(Gate::denies('add_courier'), 403);
        $this->validate($request, [
            'name'      => 'required',
            'code'      => 'required',
            'logo'      => 'mimes:png,jpg,jpeg'
        ]);

        $logo   = $request->logo ? $this->uploadImage($request, 'logo', 'couriers') : '';
        $this->courierObserver->createData($request, $logo);
        return redirect()->route('courier.index')->with(['flash' => 'Data Kurir berhasil di tambahkan']);
    }

    public function edit(Request $request, Courier $courier)
    {

        abort_if(Gate::denies('expedition_update'), 403);
        $this->validate($request, [
            'name'      => 'required',
            'code'      => 'required',
            'logo'      => 'mimes:png,jpg,jpeg'
        ]);

        $logo   = $request->logo ? $this->uploadImage($request, 'logo', 'couriers') : '';
        $this->courierObserver->updateData($request, $courier, $logo);
        return redirect()->route('courier.index')->with(['flash' => 'Data Kurir berhasil di perbaharui']);
    }

    public function delete(Courier $courier)
    {

        abort_if(Gate::denies('expedition_delete'), 403);

        $courier->delete();
        return redirect()->back()->with(['flash' => 'Data kurir berhasil di hapus']);
    }
}
