<?php

namespace App\Observers\Crm;

use App\Models\Admin\Customer;
use Illuminate\Http\Request;

class CustomerObserver
{
    public function getData(Request $request)
    {
        return Customer::orderBy('default', 'asc')->orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%')->orWhere('email', 'like', '%' . $request->name . '%') : '';
        })->where(function ($query) use ($request) {
            return $request->umur != null ?  $query->whereHas('due_history', function ($q) use ($request) {
                $q->whereRaw('DATEDIFF(NOW(), created_at) >= ?', [$request->umur])->where('status', 'due')->where("total_due_amount", ">", 0);
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->umur_saldo != null ?  $query->whereHas('saldo_history', function ($q) use ($request) {
                $q->whereRaw('DATEDIFF(NOW(), created_at) >= ?', [$request->umur_saldo])->where('status', 'due')->where("total_due_amount", ">", 0);
            }) : '';
        });
    }

    public function createData(Request $request)
    {
        return Customer::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'address'           => $request->address,
            'is_account'        => $request->is_account == true ? 'yes' : 'no',
            'term_payment'      => $request->term['id'],
            'debt'              => $request->debt['id'],
            'debt_imprest'      => $request->debt_imprest['id'],
            'detail'            => $request->detail,
            'tax_default'       => $request->type == 'general' ? $request->tax_default : 'no',
            'npwp'              => $request->type == 'general' ? $request->npwp : null,
            'type'              => $request->type,
            'tax_option'        => $request->tax_option,
        ]);
    }

    public function updateData(Request $request, Customer $customer)
    {
        $customer->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'address'           => $request->address,
            'is_account'        => $request->is_account == true ? 'yes' : 'no',
            'term_payment'      => $request->term['id'],
            'debt'              => $request->debt['id'],
            'debt_imprest'      => $request->debt_imprest['id'],
            'detail'            => $request->detail,
            'tax_default'       => $request->type == 'general' ? $request->tax_default : 'no',
            'npwp'              => $request->type == 'general' ? $request->npwp : null,
            'type'              => $request->type,
            'tax_option'        => $request->tax_option,
        ]);
    }
}
