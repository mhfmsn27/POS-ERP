<?php

namespace App\Observers\Master;

use App\Models\Account\Account;
use App\Models\Transaction\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodObserver
{

    public function getData(Request $request)
    {
        return PaymentMethod::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        });
    }

    public function createAutomatic(Account $account)
    {
        $payment = PaymentMethod::create([
            'name'          => $account->name,
            'account_id'    => $account->id,
        ]);

        $account->update([
            'bank_id'   => $payment->id
        ]);

        return $payment;
    }

    public function updateAutomatic(Account $account)
    {

        if ($account->bank) {
            $account->bank()->update([
                'name'      => $account->name
            ]);
        }
    }

    public function deleteAutomatic(Account $account)
    {
        PaymentMethod::where("account_id", $account->id)->delete();
    }


    public function createData(Request $request, String $image)
    {
        $paymentMethod = PaymentMethod::create([
            'name'              => $request->name,
            'service'           => $request->service == true ? 'yes' : 'no',
            'amount'            => $request->service == true ? $request->amount : 0,
            'account_id'        => $request->account['id'],
            'automatic_sync'    => 'no',
            'an'                => $request->an,
            'no_rek'            => $request->no_rek,
            'logo'              => $image
        ]);

        if ($paymentMethod->account_id != null) {
            $paymentMethod->account()->update([
                'bank_id'           => $paymentMethod->id
            ]);
        }
    }

    public function updateData(Request $request, PaymentMethod $method, String $image = '')
    {
        $method->update([
            'name'              => $request->name,
            'service'           => $request->service == true ? 'yes' : 'no',
            'amount'            => $request->service == true ? $request->amount : 0,
            'account_id'        => $request->account['id'],
            'automatic_sync'    => 'no',
            'an'                => $request->an,
            'no_rek'            => $request->no_rek,
            'logo'              => $image == '' ? $method->logo : $image
        ]);

        if ($method->account_id != null) {
            $method->account()->update([
                'bank_id'           => $method->id
            ]);
        }
    }

    public function deleteData(PaymentMethod $method)
    {
    }
}
