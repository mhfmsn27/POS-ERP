<?php

namespace App\Observers\Account;

use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use Illuminate\Http\Request;

class LedgerObserver
{
    public function getData(Request $request)
    {
        return Account::orderBy('coa', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%')->orWhere('coa', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->only_parent != null && $request->parent == null ? $q->where('is_root_parent', $request->only_parent) : '';
        })->where(function ($q) use ($request) {
            return $request->parent ? $q->where('parent_id', $request->parent)->orWhere("id", $request->parent) : '';
        })->where(function ($q) use ($request) {
            return $request->type ? $q->where("account_type_id", $request->type) : '';
        })->whereHas('type', function ($q) use ($request) {
            return $request->price ? ($request->price == 'without_bank' ? $q->where("type", "!=", "bank_cash") : $q->where("type", $request->price)) : '';
        })->where(function ($q) use ($request) {
            return $request->cashflow ? $q->where("cashflow", $request->cashflow) : '';
        })->where(function ($q) use ($request) {
            return $request->without_data ? $q->where("id", "!=", $request->without_data) : '';
        })->whereHas('type', function ($q) use ($request) {
            return $request->default ? $q->where("default", $request->default) : '';
        });
    }

    public function getByType(String $accountId, String $date)
    {
        return AccountTransaction::where(function($q) use ($accountId) {
            return $accountId != '' ? $q->where("account_id", $accountId) : '';
        })->where(function($q) use ($date) {
            return $date != '' ? $q->whereDate("operation_date", "<=", $date) : '';
        })->orderBy('operation_date','desc')->orderBy('id','desc')->first(); 
    }

    public function checkReadyCode($param, $type = 'code')
    {
        return Account::where(function ($q) use ($type, $param) {
            return $type == 'code' ? $q->where("coa", $param) : $q->where("id", $param);
        })->first();
    }

    public function createData(Request $request): Account
    {
        return Account::create([
            'name'                  => $request->name,
            'coa'                   => $request->subtype == true && $request->autocode == true ? $this->generateCodeCoa($request->account['id']) : $request->coa,
            'account_type_id'       => $request->type['id'],
            'created_by'            => auth()->user()->id,
            'note'                  => $request->note,
            'is_root_parent'        => $request->subtype == true ? 'yes' : 'no',
            'parent_id'             => $request->subtype == true ? $request->account['id'] : null
        ]);
    }

    public function updateData(Request $request, Account $account): Account
    {
        $account->update([
            'name'                  => $request->name,
            'coa'                   => $request->coa,
            'created_by'            => auth()->user()->id,
            'note'                  => $request->note,
            'is_root_parent'        => $request->subtype == true ? 'yes' : 'no',
            'parent_id'             => $request->subtype == true ? $request->account['id'] : null
        ]);

        return $account;
    }

    public function generateCodeCoa($accountId)
    {
        $account    = Account::where("id", $accountId)->first();
        $count      = Account::where("parent_id", $accountId)->withTrashed()->count() + 1;
        $number     = sprintf("%02d", $count);
        return      $account->coa . '' . $number;
    }

    public function updateCashFlowAccount(Account $account)
    { 
       
        $account->update([
            'cashflow'      => $account->balance_account
        ]);
    }
}
