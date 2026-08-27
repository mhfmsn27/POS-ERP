<?php

namespace Poshub\Ecommerce\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Poshub\Ecommerce\Models\CustomerAddress;
use Poshub\Ecommerce\Repositories\AddressRepository;

class AddressController extends Controller
{

    /**
     * Mengambil atau Mengintegrasikan fungsi AddressRepository
     */

    protected $addressRepository;
    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository   = $addressRepository;
    }

    public function index()
    {
        $data = $this->addressRepository->getData();
        return view('ecommerce::mobile.account.address.index', ['page' => 'Alamat Saya'], compact('data'));
    }

    public function create()
    {
        return view('ecommerce::mobile.account.address.create', ['page' => 'Tambah Alamat']);
    }

    public function update(CustomerAddress $address)
    {
        return view('ecommerce::mobile.account.address.update', ['page' => 'Edit Alamat'], compact('address'));
    }

    public function delete(CustomerAddress $address)
    {
        if ($address->default == 'yes') {
            return redirect()->back()->with(['gagal' => 'Kamu tidak bisa menghapus alamat default']);
        }

        $address->delete();

        return redirect()->with(['flash' => 'Hapus data berhasil di lakukan'])->route('ecommerce.mobile.address.index');
    }
}
