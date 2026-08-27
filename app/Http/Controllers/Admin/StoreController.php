<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Observers\Saas\StoreObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    protected $storeObserver;

    public function __construct(StoreObserver $storeObserver)
    {
        $this->storeObserver = $storeObserver;
    }

    /**
     * Display listing of enterprise stores/branches for switching.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Store::query();

        if ($user && $user->store_id != '0' && !empty($user->store_id)) {
            $storeIds = explode(',', (string)$user->store_id);
            $query->whereIn('id', $storeIds);
        }

        $data = $query->orderBy('name', 'asc')->get();

        // If only 1 store exists, auto-select and proceed
        if ($data->count() === 1) {
            session(['mystore' => $data->first()->id]);
            return redirect()->route('page.home');
        }

        return view('auth.choose_store', [
            'page' => 'Pilih Cabang / Toko',
            'data' => $data
        ]);
    }

    /**
     * Choose active store / branch.
     */
    public function choose($store)
    {
        $storeModel = Store::find($store);
        if ($storeModel) {
            session(['mystore' => $storeModel->id]);
            return redirect()->route('page.home');
        }

        return redirect()->route('store.choose')->with(['gagal' => 'Toko tidak ditemukan']);
    }

    /**
     * Show form to create new branch / outlet.
     */
    public function create()
    {
        return view('admin.settings.store.create', [
            'page' => 'Tambah Cabang Baru'
        ]);
    }

    /**
     * Store a newly created branch.
     */
    public function createData(Request $request)
    {
        $this->validate($request, [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email',
            'phone'   => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        try {
            $store = $this->storeObserver->createData($request, 1);
            return redirect()->route('store.choose')->with(['sukses' => 'Cabang berhasil ditambahkan']);
        } catch (\Throwable $e) {
            return back()->with(['gagal' => 'Gagal menambahkan cabang: ' . $e->getMessage()]);
        }
    }

    /**
     * Update branch settings.
     */
    public function update(Request $request)
    {
        $storeId = my_store();
        $store = Store::findOrFail($storeId);

        return view('admin.settings.store.update', [
            'page' => 'Pengaturan Cabang',
            'store' => $store
        ]);
    }

    /**
     * Delete branch.
     */
    public function delete($store)
    {
        $storeModel = Store::findOrFail($store);
        $storeModel->delete();

        return redirect()->route('store.choose')->with(['sukses' => 'Cabang berhasil dihapus']);
    }
}
