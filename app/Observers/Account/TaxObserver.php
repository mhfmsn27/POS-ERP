<?php

namespace App\Observers\Account;

use App\Models\Account\AccountTransaction;
use App\Models\Account\SptTax;
use App\Models\Account\SptTaxDetail;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\Store;
use App\Models\Admin\Taxrate;
use Illuminate\Http\Request;

class TaxObserver
{

    protected $ledgerObserver;
    protected $ledgerTransactionObserver;

    public function __construct(LedgerObserver $ledgerObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->ledgerObserver               = $ledgerObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }


    public function getData(Request $request)
    {

        return AccountTransaction::where(function ($q) use ($request) {
            return $request->sub_type ? $q->where("sub_type", $request->sub_type) : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("tax_paid", $request->status) : '';
        })->where(function ($q) use ($request) {
            return $request->status_payment ? $q->where("tax_status", $request->status_payment) : '';
        })->where(function ($q) use ($request) {
            return $request->name ? $q->whereHas('transaction', function ($q) use ($request) {
                return $q->where('ref_no', 'like', '%' . $request->name . '%');
            }) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('operation_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("operation_date", $request->start_date) : "";
            }
        })->where('transaction_id', '!=', null)->where("store_id", my_store())->orderBy("created_at", "desc");
    }

    public function getSpt(Request $request)
    {
        return SptTax::where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->orderBy("created_at", "desc");
    }

    public function getDataByParam(Request $request, String $type, String $status, String $account, $for = '')
    {

        return AccountTransaction::where(function ($q) use ($account) {
            return $account != '' ? $q->where("sub_type", $account) : '';
        })->where(function ($q) use ($status) {
            return $status != '' ? $q->where("tax_paid", $status) : '';
        })->where(function ($q) {
            return $q->where("tax_status", 'pending');
        })->where(function ($q) use ($request, $for) {
            if ($request->end_date && $request->start_date) { 
                //  return $for == '' ?  $q->whereBetween('operation_date', [$request->start_date, now()->parse($request->end_date)->addDay()]) : $q->whereDate('operation_date', "<=", $request->end_date);
                return $q->whereDate('operation_date', "<=", $request->end_date);
            } else { 
                return $request->start_date ? $q->whereDate("operation_date", $request->start_date) : "";
            }
        });
    }

    public function createData(Request $request)
    {

        $settings   = AccountSetting::first(['tax_input', 'tax_output', 'tax_minus', 'tax_over']);
        $data       = SptTax::create([
            'start_date'        => $request->date['start'],
            'end_date'          => $request->date['end'],
            'ntpt'              => $request->payment['ntpt'],
            'payment_date'      => $request->payment['date'],
            'amount'            => $request->payment['amount'],
            'type'              => $request->payment['type'],
            'note'              => $request->note
        ]);

        $keluaran   = 0;
        $masukan    = 0;

        if ($request->lebih > 0) {
            $masukan    = $request->keluaran;
            $keluaran   = $request->keluaran;
        } else {
            $masukan    = $request->masukan;
            $keluaran   = $request->keluaran;
        }


        if ($settings->tax_input_account) {
            $masukanAccount    = AccountTransaction::create([
                'account_id'                    => $settings->tax_input,
                'spt_taxes_id'                  => $data->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $masukan,
                'type'                          => 'credit',
                'sub_type'                      => 'spt_tax',
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $data->created_at),
                'operation_date'                => $data->end_date . ' ' . date('H:i:s'),
                'name'                          => 'SPT - ' . $data->created_at->format('Y-m-d')
            ]);

            $this->ledgerObserver->updateCashFlowAccount($settings->tax_input_account);
            $this->ledgerTransactionObserver->logAccountTransaction($masukanAccount);
        }

        if ($settings->tax_output_account) {
            $keluaranAccount    = AccountTransaction::create([
                'account_id'                    => $settings->tax_output,
                'spt_taxes_id'                  => $data->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $keluaran,
                'type'                          => 'debit',
                'sub_type'                      => 'spt_tax',
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $data->created_at),
                'operation_date'                => $data->end_date . ' ' . date('H:i:s'),
                'name'                          => 'SPT - ' . $data->created_at->format('Y-m-d')
            ]);

            $this->ledgerObserver->updateCashFlowAccount($settings->tax_output_account);
            $this->ledgerTransactionObserver->logAccountTransaction($keluaranAccount);
        }

        if ($settings->tax_over_account && $request->lebih > 0) {
            $taxOver    = AccountTransaction::create([
                'account_id'                    => $settings->tax_over_account->id,
                'spt_taxes_id'                  => $data->id,
                'created_by'                    => auth()->user()->id,
                'amount'                        => $request->lebih,
                'type'                          => 'debit',
                'sub_type'                      => 'spt_tax',
                'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('spt_tax', $data->created_at),
                'operation_date'                => $data->end_date . ' ' . date('H:i:s'),
                'name'                          => 'SPT - ' . $data->created_at->format('Y-m-d')
            ]);


            $this->ledgerObserver->updateCashFlowAccount($settings->tax_over_account);
            $this->ledgerTransactionObserver->logAccountTransaction($taxOver);
        }

        if ($settings->tax_minus_account && $request->kurang > 0) {
            $lebihBayar = $settings->tax_over_account->cashflow ?? 0;

            if ($lebihBayar > 0) {
                if ($lebihBayar > $request->kurang) {
                    $taxOver    = AccountTransaction::create([
                        'account_id'                    => $settings->tax_over_account->id,
                        'spt_taxes_id'                  => $data->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $request->kurang,
                        'type'                          => 'credit',
                        'sub_type'                      => 'spt_tax',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('spt_tax', $data->created_at),
                        'operation_date'                => $data->end_date . ' ' . date('H:i:s'),
                        'name'                          => 'SPT - ' . $data->created_at->format('Y-m-d')
                    ]);


                    $this->ledgerObserver->updateCashFlowAccount($settings->tax_over_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxOver);
                } else {
                    $mustDebit = $request->kurang - $lebihBayar;
                    $taxOver    = AccountTransaction::create([
                        'account_id'                    => $settings->tax_over_account->id,
                        'spt_taxes_id'                  => $data->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $lebihBayar,
                        'type'                          => 'credit',
                        'sub_type'                      => 'spt_tax',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('spt_tax', $data->created_at),
                        'operation_date'                => $data->end_date . ' ' . date('H:i:s'),
                        'name'                          => 'SPT - ' . $data->created_at->format('Y-m-d')
                    ]);


                    $this->ledgerObserver->updateCashFlowAccount($settings->tax_over_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxOver);

                    // Debit 
                    $taxMinus    = AccountTransaction::create([
                        'account_id'                    => $settings->tax_minus_account->id,
                        'spt_taxes_id'                  => $data->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $mustDebit,
                        'type'                          => 'debit',
                        'sub_type'                      => 'spt_tax',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('spt_tax', $data->created_at),
                        'operation_date'                => $data->end_date . ' ' . date('H:i:s'),
                        'name'                          => 'SPT - ' . $data->created_at->format('Y-m-d')
                    ]);


                    $this->ledgerObserver->updateCashFlowAccount($settings->tax_minus_account);
                    $this->ledgerTransactionObserver->logAccountTransaction($taxMinus);
                }
            } else {
                $taxMinus    = AccountTransaction::create([
                    'account_id'                    => $settings->tax_minus_account->id,
                    'spt_taxes_id'                  => $data->id,
                    'created_by'                    => auth()->user()->id,
                    'amount'                        => $request->kurang,
                    'type'                          => 'credit',
                    'sub_type'                      => 'spt_tax',
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('spt_tax', $data->created_at),
                    'operation_date'                => $data->end_date . ' ' . date('H:i:s'),
                    'name'                          => 'SPT - ' . $data->created_at->format('Y-m-d')
                ]);


                $this->ledgerObserver->updateCashFlowAccount($settings->tax_minus_account);
                $this->ledgerTransactionObserver->logAccountTransaction($taxMinus);
            }
        }


        return $data;
    }

    public function createDetail(SptTax $spt, String $type, Float $credit, Float $amount)
    {
        return SptTaxDetail::create([
            'spt_tax_id'        => $spt->id,
            'transaction_type'  => $type,
            'credit'            => $credit,
            'amount'            => $amount
        ]);
    }

    public function createDefault(Store $store, $merchantId = null)
    {
        $ppn =  Taxrate::create([
            'name'          => 'PPN',
            'taxrate'       => '11',
            'code'          => 'T11',
            'store_id'      => $store->id,
            'merchant_id'   => $merchantId == null ? auth()->user()->merchant_id : $merchantId
        ]);

        $ppn23 = Taxrate::create([
            'name'          => 'PPH 23',
            'taxrate'       => '2',
            'code'          => 'T23',
            'store_id'      => $store->id,
            'merchant_id'   => $merchantId == null ? auth()->user()->merchant_id : $merchantId
        ]);

        $ppn22 = Taxrate::create([
            'name'          => 'PPH 22',
            'taxrate'       => '2,5',
            'code'          => 'T22',
            'store_id'      => $store->id,
            'merchant_id'   => $merchantId == null ? auth()->user()->merchant_id : $merchantId
        ]);
    }
}
