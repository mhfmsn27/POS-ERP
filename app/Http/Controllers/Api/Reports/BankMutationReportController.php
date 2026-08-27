<?php

namespace App\Http\Controllers\Api\Reports;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reports\BankMutationResource;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Observers\Account\LedgerTransactionObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class BankMutationReportController extends Controller
{

    protected $ledgerTransactionObserver;

    public function __construct(LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->ledgerTransactionObserver        = $ledgerTransactionObserver;
    }


    public function index(Request $request)
    {
 
        abort_if(Gate::denies('mutation_view'), 403);

        $limit      = $request->limit ? $request->limit : 10;
        $data       = $this->ledgerTransactionObserver->getData($request, 'asc');
        $account    = Account::where("id", $request->account)->first(['id', 'name', 'coa','cashflow']);
        $startDate  = substr($request->start_date, 0, 10);
        $endDate    = substr($request->end_date, 0, 10);
        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);
        $firstSaldo     = null;
        $lastSaldo      = null;
        $totalCredit    = $account->total_by_type($startDate, $endDate, 'credit');
        $totalDebit     = $account->total_by_type($startDate, $endDate, 'debit');
 
        if ($firstSaldo) {
            $firstSaldo = $firstSaldo->last_log->amount ?? 0;
        } else {
            $firstSaldo = 0;
        }

        if ($firstSaldo) {
            $lastSaldo = $lastSaldo->last_log->amount ?? 0;
        } else {
            $lastSaldo = 0;
        }

        return response()->json([
            'totalRows'     => $totalRows,
            'account'       => array(
                'name'          => $account != null ? $account->name : '',
                'code'          => $account != null ? $account->coa : '',
                'first_saldo'   => (float)$firstSaldo,
                'last_saldo'    => (float)$lastSaldo,
                'saldo'         => $account != null ? number_format($account->cashflow) : 0,
                'credit'        => (float)$totalCredit,
                'debit'         => (float)$totalDebit
            ),
            'transactions'  => BankMutationResource::collection($transactions),
        ], 200);
    }
}
