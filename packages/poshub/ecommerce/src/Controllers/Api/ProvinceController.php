<?php

namespace Poshub\Ecommerce\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Poshub\Ecommerce\Models\City;
use Poshub\Ecommerce\Models\Province;
use Poshub\Ecommerce\Models\SubDistrict;
use Yajra\DataTables\Facades\DataTables;

class ProvinceController extends Controller
{

      public function index()
      {
            $data = Province::orderBy('name', 'asc')->get();
            return view('ecommerce::admin.setting.province', ['page' => 'Daftar Provinsi'], compact('data'));
      }

      public function city()
      {
            $data = City::orderBy('name', 'asc')->get();
            return view('ecommerce::admin.setting.city', ['page' => 'Daftar Kota / Kabupaten'], compact('data'));
      }

      public function district(Request $request)
      {
            if ($request->ajax()) {
                  $data = SubDistrict::orderBy("name", "asc");

                  return DataTables::of($data)
                        ->addColumn(
                              'action',
                              function ($row) {

                                    if ($row->status == 'yes') {
                                          $html =  '<a href="' . route('ecommerce.sett.district.status', $row->id) . '" class="btn btn-sm btn-danger"><i class="fa fa-times-circle"></i> Non Aktif</a>';
                                    } else {
                                          $html =  '<a href="' . route('ecommerce.sett.district.status', $row->id) . '" class="btn btn-sm btn-success"><i class="fa fa-plus-circle"></i> Aktifkan</a>';
                                    }


                                    return $html;
                              }
                        )->addColumn('city_name', function ($row) {
                              return  $row->city->name ?? '';
                        })->addColumn('city_type', function ($row) {
                              return  $row->city->type ?? '';
                        })->addColumn('province_name', function ($row) {
                              return  $row->city->province->name ?? '';
                        })->addColumn('my_status', function ($row) {
                              return  $row->status == 'yes' ? 'Aktif' : 'Tidak Aktif';
                        })->rawColumns(['action'])->make(true);
            }
            
            return view('ecommerce::admin.setting.district', ['page' => 'Daftar Kecamatan']);
      }

      public function updateProvince(Province $province)
      {
            if ($province->status == 'yes') {
                  $province->update([
                        'status'    => 'no'
                  ]);
            } else {
                  $province->update([
                        'status'    => 'yes'
                  ]);
            }

            return back()->with(['flash' => 'Berhasil memperbaharui data']);
      }

      public function updateCity(City $city)
      {
            if ($city->status == 'yes') {
                  $city->update([
                        'status'    => 'no'
                  ]);
            } else {
                  $city->update([
                        'status'    => 'yes'
                  ]);
            }

            return back()->with(['flash' => 'Berhasil memperbaharui data']);
      }

      public function updateDistrict(SubDistrict $district)
      {
            if ($district->status == 'yes') {
                  $district->update([
                        'status'    => 'no'
                  ]);
            } else {
                  $district->update([
                        'status'    => 'yes'
                  ]);
            }

            return back()->with(['flash' => 'Berhasil memperbaharui data']);
      }
}
