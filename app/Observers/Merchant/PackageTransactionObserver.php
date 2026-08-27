<?php

namespace App\Observers\Merchant;

use App\Models\Admin\InternalSetting;
use App\Models\Admin\Package;
use App\Models\Admin\Store;
use App\Models\Transaction\TransactionPackage;
use App\Models\Transaction\TransactionPackagePayment;
use DateTime;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class PackageTransactionObserver
{

    public function getData(Request $request)
    {
        return TransactionPackage::where(function ($q) {
            return auth()->user()->role_type == 'user' ? $q->where("merchant_id", auth()->user()->merchant_id) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->statusA) : '';
        })->where(function ($q) use ($request) {
            return $request->store ? $q->where("store_id", $request->store) : '';
        })->whereHas('store', function ($q) use ($request) {
            return $request->merchant ? $q->where("merchant_id", $request->merchant) : '';
        });
    }

    public function createData(Store $store, Package $package)
    {

        $addExpireDate          = now()->addDays((int)$package->limit_day);
        $getLastTransaction     = $this->getLastTransactionBusiness($store);
        $getDaysTransaction     = $this->getAddDaysTransaction($package, $getLastTransaction);
        $settings               = InternalSetting::first(['tax']);
        $taxrate                = $settings->tax;
        $taxTotal               = $taxrate > 0 ? $taxrate / 100 * $package->price : 0;

        if ($getDaysTransaction['status'] == true) {
            $addExpireDate      = $getDaysTransaction['new_date'];
        }

        return TransactionPackage::create([
            'ref_no'            => rand(),
            'merchant_id'       => $store->merchant_id,
            'store_id'          => $store->id,
            'package_id'        => $package->id,
            'end_date'          => $addExpireDate,
            'subtotal'          => $package->price,
            'tax'               => $taxrate,
            'grand_total'       => ($package->price + $taxTotal)
        ]);
    }

    public function createPayment(TransactionPackage $transaction)
    {
        return TransactionPackagePayment::create([
            'transaction_package_id'        => $transaction->id,
            'amount'                        => $transaction->grand_total,
            'order_id'                      => 'inv-' . Uuid::uuid4()->toString()
        ]);
    }

    public function getLastTransactionBusiness(Store $store)
    {
        return TransactionPackage::where("status", "success")->where("store_id", $store->id)->orderBy("created_at", "desc")->first(['id', 'end_date']);
    }

    public function getAddDaysTransaction($package, $getLastTransaction)
    {
 
        if ($getLastTransaction != null) {
            if ($getLastTransaction->end_date >= now()) {
                $datetime1 = new DateTime($getLastTransaction->end_date);
                $datetime2 = new DateTime(now());
                $interval = $datetime1->diff($datetime2);

                $totalY     = 0;
                if ($interval->y > 0) {
                    $totalY     = $interval->y * 365;
                }

                $totalM     = 0;
                if ($interval->m > 0) {
                    $totalM     = $interval->m * 30;
                }

                $addDays = $package->limit_day + ($interval->d + $totalY + $totalM); 
                return array(
                    'status'    => true,
                    'new_date'  => now()->addDays($addDays)
                );
            }
        }

        return array(
            'status'    => false
        );
    }
}
