<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Admin\Package;
use App\Observers\Administrator\PackageObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{

    protected $packageObserver;
    public function __construct(PackageObserver $packageObserver)
    {
        $this->packageObserver      = $packageObserver;
    }


    public function index(Request $request)
    {
        $packages   = $this->packageObserver->getData($request)->get();
        return view('super.package.index', ['page' => 'Daftar Paket Harga'], compact('packages'));
    }

    public function create(Request $request)
    {
        return view('super.package.create', ['page' => 'Tambah Paket']);
    }

    public function update(Request $request, Package $package)
    {
        $this->validate($request, [
            'name'          => 'required',
            'price'         => 'required',
            'limit_day'     => 'required|numeric',
        ]);

        try {

            DB::beginTransaction();

            $package->details()->delete();

            $this->packageObserver->updateData($request, $package);
            $this->packageObserver->createDetail($request, $package);

            DB::commit();

            return redirect()->route('admin.package.index')->with(['flash' => 'Package berhasil di perbaharui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name'          => 'required',
            'price'         => 'required',
            'limit_day'     => 'required|numeric',
        ]);

        try {

            DB::beginTransaction();

            $package = $this->packageObserver->createData($request);
            $this->packageObserver->createDetail($request, $package);

            DB::commit();

            return redirect()->route('admin.package.index')->with(['flash' => 'Package berhasil di tambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

    public function edit(Package $package)
    {
        return view('super.package.update', ['page' => 'Edit Paket'], compact('package'));
    }

    public function delete(Package $package)
    {
        try {

            DB::beginTransaction();

            $package->details()->delete();
            $package->delete();

            DB::commit();

            return redirect()->route('admin.package.index')->with(['flash' => 'Package berhasil di hapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }
}
