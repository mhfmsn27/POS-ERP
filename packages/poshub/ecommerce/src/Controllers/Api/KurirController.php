<?php

namespace Poshub\Ecommerce\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\EcommerceShipping;

class KurirController extends Controller
{

      public function index()
      {
            $data = EcommerceShipping::orderBy('name', 'asc')->get();
            return view('ecommerce::admin.curir.index', ['page' => 'Daftar Kurir'], compact('data'));
      }

      public function create()
      {
            return view('ecommerce::admin.curir.create', ['page' => 'Tambah Kurir']);
      }


      public function update(EcommerceShipping $curir)
      {
            return view('ecommerce::admin.curir.update', ['page' => 'Edit Kurir'], compact('curir'));
      }


      public function store(Request $request)
      {

            try {

                  $this->validate($request, [
                        'name'      => 'required',
                        'status'    => 'required|in:yes,no',
                        'code'      => 'required',
                        'logo'      => 'mimes:jpg,jpeg,png,svg'
                  ]);


                  EcommerceShipping::create([
                        'name'      => $request->name,
                        'status'    => $request->status,
                        'code'      => $request->code,
                        'logo'      => $request->logo ? $this->uploadImage($request, 'logo', 'ecommerce/curir') : 'uploads/slider/image.jpg',
                  ]);

                  return response()->json([
                        'message'         => 'Data Kurir Berhasil Di Tambahkan',
                        'status'          => true
                  ], 200);
            } catch (\Illuminate\Validation\ValidationException $exception) {
                  return response()->json([
                        'errors'          => $exception->errors(),
                        'message'         => 'Terjadi Kesalahan',
                        'status'          => false
                  ], 200);
            }
      }

      public function edit(Request $request, EcommerceShipping $curir)
      {

            try {

                  $this->validate($request, [
                        'name'      => 'required',
                        'status'    => 'required|in:yes,no',
                        'code'      => 'required',
                        'logo'      => 'mimes:jpg,jpeg,png,svg'
                  ]);

                  $curir->update([
                        'name'      => $request->name,
                        'status'    => $request->status,
                        'code'      => $request->code,
                        'logo'      => $request->logo ? $this->uploadImage($request, 'logo', 'ecommerce/curir') : $curir->logo,
                  ]);

                  return response()->json([
                        'message'         => 'Data Kurir Berhasil Di Perbaharui',
                        'status'          => true
                  ], 200);
            } catch (\Illuminate\Validation\ValidationException $exception) {
                  return response()->json([
                        'errors'          => $exception->errors(),
                        'message'         => 'Terjadi Kesalahan',
                        'status'          => false
                  ], 200);
            }
      }

      public function delete(EcommerceShipping $curir)
      {
            $curir->delete();
            return back()->with(['flash' => 'Data Kurir Berhasil Di Hapus']);
      }
}
