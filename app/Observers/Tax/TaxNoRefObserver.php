<?php

namespace App\Observers\Tax;

use App\Models\Tax\TaxNoRef;
use App\Models\Tax\TaxNoRefDetail;
use Illuminate\Http\Request;

class TaxNoRefObserver
{
    public function getData(Request $request)
    {
        return TaxNoRef::where(function ($q) use ($request) {
            return $request->name ?  $q->where('from_number', 'like', '%' . $request->name . '%')->orWhere('to_number', 'like', '%' . $request->name . '%') : '';
        })->orderBy("id", "desc");
    }

    public function getDetail(Request $request, TaxNoRef $taxes)
    {
        return TaxNoRefDetail::where(function ($q) use ($request) {
            return $request->name ?  $q->where('number', 'like', '%' . $request->name . '%')->orWhere(function ($q) use ($request) {
                return $q->whereHas("transaction", function ($q) use ($request) {
                    return $request->name ? $q->where('ref_no', 'like', '%' . $request->name . '%') : '';
                });
            }) : '';
        })->where("tax_no_ref_id", $taxes->id)->orderBy("id", "desc");
    }

    public function createData(Request $request)
    {
        return TaxNoRef::create([
            'from_number'       => $request->from,
            'to_number'         => $request->to,
            'type'              => $request->type,
        ]);
    }

    public function createDetails(TaxNoRef $taxes)
    {
        $fromPrefix = explode(".", $taxes->from_number);

        $company    = $fromPrefix[0];
        $year       = $fromPrefix[1];
        $prefix     = $company . '.' . $year . '.';

        $startNum   = (int) str_replace($prefix, '', $taxes->from_number);
        $endNum     = (int) str_replace($prefix, '', $taxes->to_number);

        for ($i = $startNum; $i <= $endNum; $i++) {

            $numbers = $prefix . str_pad($i, 8, '0', STR_PAD_LEFT);
            TaxNoRefDetail::create([
                'tax_no_ref_id'     => $taxes->id,
                'number'            => $numbers
            ]);
        }
    }

    public function checkData($number)
    {
        return TaxNoRefDetail::whereHas('tax_ref', function ($q) {
            return $q->where('store_id', my_store());
        })->where("number", $number);
    }

    public function getNumberNew()
    {
        $lastData   = $this->getLastUse();
        return TaxNoRefDetail::whereHas('tax_ref', function ($q) {
            return $q->where('store_id', my_store());
        })->where(function ($q) use ($lastData) {
            return $lastData ? $q->where("number", ">", $lastData->number) : '';
        })->where("transaction_id", null)->where('number', 'like', '%.' . substr(date('Y'), 2, 2) . '.%')->orderBy("number", "asc")->first(['id', 'number']);
    }

    public function getLastUse()
    {
        return TaxNoRefDetail::whereHas('tax_ref', function ($q) {
            return $q->where('store_id', my_store());
        })->where("transaction_id", "!=", null)->where('number', 'like', '%.' . substr(date('Y'), 2, 2) . '.%')->orderBy("number", "desc")->first(['id', 'number']);
    }
}
