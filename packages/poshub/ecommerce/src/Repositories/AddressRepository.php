<?php

namespace Poshub\Ecommerce\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Poshub\Ecommerce\Models\City;
use Poshub\Ecommerce\Models\CustomerAddress;
use Poshub\Ecommerce\Models\Province;
use Poshub\Ecommerce\Models\SubDistrict;

class AddressRepository
{
    public function getData()
    {
        return CustomerAddress::where("customer_id", Auth::guard('customers')->user()->id)->orderBy("id", "desc")->get();
    }

    public function create(Object $data)
    {

        if ($data->default == 'yes') {
            CustomerAddress::where("customer_id", Auth::guard('customers')->user()->id)->update([
                'default'   => 'no'
            ]);
        }

        $default = $data->default;

        if ($data->default == 'no') {
            if ($this->defaultAddress() == null) {
                $default = 'yes';
            }
        }

        $address = CustomerAddress::create([
            'customer_id'           => Auth::guard('customers')->user()->id,
            'name'                  => $data->name,
            'sub_district_id'       => $data->sub_district_id,
            'address'               => $data->address,
            'postal_code'           => $data->postal_code,
            'phone'                 => $data->phone,
            'default'               => $default,
            'long'                  => isset($data->long) ? $data->long : null,
            'lang'                  => isset($data->lang) ? $data->lang : null
        ]);


        return $address;
    }

    public function update(CustomerAddress $address, Object $data)
    {

        if ($data->default == 'yes') {
            CustomerAddress::where("customer_id", Auth::guard('customers')->user()->id)->where("id", "!=", $address->id)->update([
                'default'   => 'no'
            ]);
        }

        $address->update([
            'customer_id'           => Auth::guard('customers')->user()->id,
            'name'                  => $data->name,
            'sub_district_id'       => $data->sub_district_id,
            'address'               => $data->address,
            'postal_code'           => $data->postal_code,
            'phone'                 => $data->phone,
            'default'               => $data->default,
            'long'                  => isset($data->long) ? $data->long : null,
            'lang'                  => isset($data->lang) ? $data->lang : null
        ]);

        return $address;
    }

    public function defaultAddress()
    {
        return  CustomerAddress::where("customer_id", Auth::guard('customers')->user()->id)->where("default", "yes")->first();
    }

    public function getProvince(Request $request)
    {
        return Province::where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->orderBy("name", "asc")->limit(20)->get();
    }

    public function getCity(Request $request)
    {
        return City::where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->province != null || $request->province != '' ? $q->where("province_id", $request->province) : '';
        })->orderBy("name", "asc")->limit(20)->get();
    }

    public function getSubdistrict(Request $request)
    {
        return SubDistrict::where(function ($q) use ($request) {
            return $request->term ? $q->where('name', 'like', '%' . $request->term . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->city != null || $request->city != '' ? $q->where("city_id", $request->city) : '';
        })->orderBy("name", "asc")->limit(20)->get();
    }
}
