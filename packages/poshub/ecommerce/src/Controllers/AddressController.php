<?php

namespace Poshub\Ecommerce\Controllers;

use App\Http\Controllers\Controller;
use Poshub\Ecommerce\Models\CustomerAddress;
use Poshub\Ecommerce\Repositories\AddressRepository;
use Poshub\Ecommerce\Requests\AddressRequest;
use Poshub\Ecommerce\Resources\AddressResource;

class AddressController extends Controller
{


      /*
      |--------------------------------------------------------------------------
      | Address Controller
      |--------------------------------------------------------------------------
      |
      | Kumpulan Fungsi Fungsi untuk mengatur parameters Alamat Pelanggan
      | Adapun permasalahan dengan basis data
      | bisa di check di bagian AddressRepository
      |
      */

      /**
       * Mengambil atau Mengintegrasikan fungsi AddressRepository
       */

      protected $addressRepository;
      public function __construct(AddressRepository $addressRepository)
      {
            $this->addressRepository   = $addressRepository;
      }

      /**
       * Get Data Address for dom jquery
       */

      public function index()
      {
            $data = $this->addressRepository->getData();

            return response()->json([
                  'data'     => AddressResource::collection($data),
                  'status'    => true
            ]);
      }

      public function detail(CustomerAddress $address)
      {
            return response()->json([
                  'data'     => AddressResource::make($address),
                  'status'    => true
            ]);
      }

      public function store(AddressRequest $request)
      {

            $address = $this->addressRepository->create($request);

            return response()->json([
                  'message'   => 'Alamat berhasil di tambahkan',
                  'data'      => AddressResource::make($address),
                  'status'    => true
            ]);
      }

      public function update(AddressRequest $request, CustomerAddress $address)
      {
            if ($request->default == 'no') {

                  $default = $this->addressRepository->defaultAddress();

                  if ($default->id == $address->id) {
                        return response()->json([
                              'message'     => 'Alamat Default Diperlukan, minimal satu alamat',
                              'status'    => false
                        ]);
                  }
            }

            $address = $this->addressRepository->update($address, $request);

            return response()->json([
                  'message'     => 'Alamat berhasil di ubah',
                  'data'     => AddressResource::make($address),
                  'status'    => true
            ]);
      }

      public function delete(CustomerAddress $address)
      {

            if ($address->default == 'yes') {
                  return response()->json([
                        'message'         => 'Kamu tidak bisa menghapus alamat default',
                        'status'          => false
                  ]);
            }

            $address->delete();

            return response()->json([
                  'message'               => 'Alamat berhasil di hapus',
                  'status'                => true
            ]);
      }
}
