<?php

namespace App\Http\Controllers\Api\Account\Tax;

use App\Exports\Taxrate\TransactionTaxExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\Account\SptDetailResource;
use App\Http\Resources\Account\SptListResource;
use App\Http\Resources\Account\TaxTransactionResource;
use App\Models\Account\AccountTransaction;
use App\Models\Account\SptTax;
use App\Models\Admin\AccountSetting;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Account\TaxObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    protected $taxObserver;
    protected $ledgerTransactionObserver;
    protected $ledgerObserver;

    public function __construct(TaxObserver $taxObserver, LedgerTransactionObserver $ledgerTransactionObserver, LedgerObserver $ledgerObserver)
    {
        $this->taxObserver                  = $taxObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
        $this->ledgerObserver               = $ledgerObserver;
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->taxObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => TaxTransactionResource::collection($transactions),
        ], 200);
    }

    public function changeAction(AccountTransaction $transaction)
    {
        try {

            if ($transaction->tax_status == 'complete') {
                return response()->json([
                    'message'   => 'Maaf, faktur pajak ini sudah di setorkan sebelumnya, anda tidak dapat mengubahnya kembali',
                    'status'    => false
                ], 422);
            }

            $transaction->update([
                'tax_paid'      => $transaction->tax_paid == 'paid' ? 'due' : 'paid'
            ]);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function summary(Request $request)
    {

        $settings           = AccountSetting::first(['tax_input', 'tax_output', 'tax_minus', 'tax_over']);

        $forPurchase        = $this->taxObserver->getDataByParam($request, 'purchase', '', 'tax_input')->sum('amount');
        $forRpurchase       = $this->taxObserver->getDataByParam($request, 'purchase_return', '', 'tax_input_return')->sum('amount');
        $forSales           = $this->taxObserver->getDataByParam($request, 'sell', '', 'tax_output')->where('tax_type', '1')->where('tax_gunggung', 'no')->sum('amount');
        $forSalesGungung    = $this->taxObserver->getDataByParam($request, 'sell', '', 'tax_output')->where('tax_gunggung', 'yes')->sum('amount');
        $forRsales          = $this->taxObserver->getDataByParam($request, 'sales_return', '', 'tax_output_return')->sum('amount');

        $forCreditPurchase  = $this->taxObserver->getDataByParam($request, 'purchase', 'due', 'tax_input')->sum('amount');
        $forCreditRpurchase = $this->taxObserver->getDataByParam($request, 'purchase_return', 'due', 'tax_input_return')->sum('amount');
        $forCreditSales     = $this->taxObserver->getDataByParam($request, 'sell', 'due', 'tax_output')->where('tax_gunggung', 'no')->sum('amount');
        $forCreditSalesGungung     = $this->taxObserver->getDataByParam($request, 'sell', 'due', 'tax_output')->where('tax_gunggung', 'yes')->sum('amount');
        $forCreditRsales    = $this->taxObserver->getDataByParam($request, 'sales_return', 'due', 'tax_output_return')->sum('amount');

        $masukanTotal       = ($forPurchase - $forCreditPurchase) - ($forRpurchase - $forCreditRpurchase);
        $keluaranTotal      = (($forSales + $forSalesGungung) - ($forCreditSales + $forCreditSalesGungung)) - ($forRsales - $forCreditRsales);
        $subtotal           = $masukanTotal - $keluaranTotal;
        $lebih              = 0;
        $kurang             = 0;

        if ($subtotal < 1) {
            $kurang         = abs($subtotal);
        } else {
            $lebih          = abs($subtotal);
        }

        $pph22          = $this->taxObserver->getDataByParam($request, 'sell', '', 'tax_output')->where('tax_type', '2')->sum('amount');
        $pph22return    = $this->taxObserver->getDataByParam($request, 'sales_return', '', 'tax_output_return')->where('tax_type', '2')->sum('amount');

        $pph23          = $this->taxObserver->getDataByParam($request, 'sell', '', 'tax_output')->where('tax_type', '3')->sum('amount');
        $pph23return    = $this->taxObserver->getDataByParam($request, 'sales_return', '', 'tax_output_return')->where('tax_type', '2')->sum('amount');

        $lebihBayar     = $settings->tax_over_account->cashflow ?? 0;
        $kurangBayar    = $settings->tax_minus_account->cashflow ?? 0;

        return response()->json([
            'detail'    => array(
                'store'     => array(
                    'name'      => my_store_detail()->name ?? '',
                    'email'     => my_store_detail()->email ?? '',
                    'phone'     => my_store_detail()->phone ?? '',
                    'address'   => my_store_detail()->address ?? ''
                ),
                'start_date'    => $request->start_date,
                'end_date'      => $request->end_date, 
                'date'      => array(
                    'start'     => $request->start_date,
                    'end'       => $request->end_date
                ),
                'purchase'  => array(
                    'ppn'       => (float)$forPurchase,
                    'credit'       => (float)$forCreditPurchase,
                ),
                'return_purchase'  => array(
                    'ppn'           => (float)$forRpurchase,
                    'credit'        => (float)$forCreditRpurchase,
                ),
                'sales'  => array(
                    'ppn'           => (float)$forSales,
                    'gunggung'      => (float)$forSalesGungung,
                    'service'       => (float)$pph23,
                    'credit'        => (float)$forCreditSales,
                    'cgunggung'     => (float)$forCreditSalesGungung
                ),
                'return_sales'  => array(
                    'ppn'           => (float)$forRsales,
                    'service'       => (float)$pph23return,
                    'credit'        => (float)$forCreditRsales,
                ),
                'terutang'      => array(
                    'masukan'   => (float)($forCreditPurchase + $forCreditRpurchase),
                    'keluaran'  => (float)(($forCreditSales + $forCreditSalesGungung) + $forCreditRsales),
                ),
                'masukan'       => (float)$masukanTotal,
                'keluaran'      => (float)$keluaranTotal,
                'kurang'        => (float)$kurang,
                'lebih'         => (float)$lebih,
                'pph'           => array(
                    'int'           => (float)$pph22,
                    'out'           => (float)$pph22return,
                    'total'         => (float)($pph22 - $pph22return)
                ),
                'service'       => (float)($pph23 - $pph23return),
                'payment'       => array(
                    'date'          => '',
                    'ntpt'          => '',
                    'type'          => $kurang > $lebih ? 'kurang' : 'lebih',
                    'amount'        => $kurang > $lebih ? (float)$kurang : (float)$lebih
                ),
                'lebih_bayar'   => (float)$lebihBayar,
                'kurang_bayar'  => (float)$kurangBayar
            ),
        ], 200);
    }

    public function store(Request $request)
    {

        try {

            DB::beginTransaction();

            $spt    = $this->taxObserver->createData($request);

            $purchase           = $this->taxObserver->getDataByParam($request, 'purchase', '', 'tax_input')->where('tax_paid','paid');
            $sales              = $this->taxObserver->getDataByParam($request, 'sell', '', 'tax_output')->where('tax_paid','paid');
            $rpurchase          = $this->taxObserver->getDataByParam($request, 'purchase_return', '', 'tax_input_return')->where('tax_paid','paid');
            $rsales             = $this->taxObserver->getDataByParam($request, 'sales_return', '', 'tax_output_return')->where('tax_paid','paid');
            $forPurchase        = $purchase->sum('amount');
            $forRpurchase       = $rpurchase->sum('amount');
            $forSales           = $sales->sum('amount');
            $forRsales          = $rsales->sum('amount');

            $forCreditPurchase  = $this->taxObserver->getDataByParam($request, 'purchase', 'due', 'tax_input')->where('tax_paid','paid')->sum('amount');
            $forCreditRpurchase = $this->taxObserver->getDataByParam($request, 'purchase_return', 'due', 'tax_input_return')->where('tax_paid','paid')->sum('amount');
            $forCreditSales     = $this->taxObserver->getDataByParam($request, 'sell', 'due', 'tax_output')->where('tax_paid','paid')->sum('amount');
            $forCreditRsales    = $this->taxObserver->getDataByParam($request, 'sales_return', 'due', 'tax_output_return')->where('tax_paid','paid')->sum('amount');

            $purchase->update([
                'tax_status'        => 'complete',
                'spt_taxes_id'      => $spt->id
            ]);

            $sales->update([
                'tax_status'        => 'complete',
                'spt_taxes_id'      => $spt->id
            ]);

            $rpurchase->update([
                'tax_status'        => 'complete',
                'spt_taxes_id'      => $spt->id
            ]);

            $rsales->update([
                'tax_status'        => 'complete',
                'spt_taxes_id'      => $spt->id
            ]);
 
            $this->taxObserver->createDetail($spt, 'purchase', $forCreditPurchase, $forPurchase);
            $this->taxObserver->createDetail($spt, 'purchase_return', $forCreditRpurchase, $forRpurchase);
            $this->taxObserver->createDetail($spt, 'sell', $forCreditSales, $forSales);
            $this->taxObserver->createDetail($spt, 'sales_return', $forCreditRsales, $forRsales);

            DB::commit();
            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 422);
        }
    }

    public function download(Request $request)
    {
        return (new TransactionTaxExport($request, $this->taxObserver))->download('pajak_keluaran.csv');
    }

    public function spt(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->taxObserver->getSpt($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => SptListResource::collection($transactions),
        ], 200);
    }

    public function delete(SptTax $spt)
    {
        try {

            DB::beginTransaction();

            foreach($spt->account_transaction->where('sub_type','spt_tax') as $transaction)
            {
                $account            = $transaction->account;
                $nextTransaction    = AccountTransaction::where(function ($query) use ($transaction) {
                    $query->where("operation_date", ">", $transaction->operation_date)
                        ->orWhere(function ($subQuery) use ($transaction) {
                            $subQuery->where("operation_date", "=", $transaction->operation_date)
                                ->where("id", "<", $transaction->id);
                        });
                })
                    ->where("account_id", $transaction->account_id)
                    ->orderBy("operation_date", 'asc')
                    ->orderBy("id", 'asc')->first(); 
                
                $transaction->delete();
    
                if ($nextTransaction) {
                    $this->ledgerTransactionObserver->logAccountUpdate($nextTransaction);
                }
    
                if ($account) {
                    $this->ledgerObserver->updateCashFlowAccount($account);
                }
            }

            foreach($spt->account_transaction->where('sub_type','!=', 'spt_tax') as $transaction)
            {
                $transaction->update([
                    'tax_status'        => 'pending',
                ]);
            }

            $spt->delete();
            
            DB::commit();
            return response()->json([
                'message'   => 'Data berhasil di hapus',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 422);
        }
    }

    public function detail(SptTax $spt)
    {
        return response()->json([
            'details'  => SptDetailResource::make($spt),
        ], 200);
    }
}
