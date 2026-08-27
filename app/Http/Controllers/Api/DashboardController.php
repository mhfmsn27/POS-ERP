<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\ActivityLogResource;
use App\Http\Resources\Dashboard\BankRekonsiliationResource;
use App\Http\Resources\Dashboard\CustomerDueResource;
use App\Http\Resources\Dashboard\StockAlertResource;
use App\Http\Resources\Dashboard\TopCustomerResource;
use App\Http\Resources\Dashboard\TopProductResource;
use App\Http\Resources\Dashboard\UserActiveResource;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use App\Models\User;
use App\Observers\ActivityLogObserver;
use App\Observers\CashIntOut\CashIntOutObserver;
use App\Observers\Hrm\KasbonObserver;
use App\Observers\Hrm\SalaryObserver;
use App\Observers\Inventory\StockObserver;
use App\Observers\Inventory\VariationObserver;
use App\Observers\Transaction\Sales\SaleReturnObserver;
use App\Observers\Transaction\Sales\SalesObserver;
use App\Observers\Transaction\TransactionDueObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $activityLog;
    protected $variationObserver;
    protected $stockObserver;
    protected $salesObserver;
    protected $saleReturnObserver;
    protected $kasbonObserver;
    protected $salaryObserver;
    protected $cashIntOutObserver;
    protected $transactionDueObserver;

    public function __construct(
        ActivityLogObserver $activityLogObserver,
        VariationObserver $variationObserver,
        StockObserver $stockObserver,
        SalesObserver $salesObserver,
        SaleReturnObserver $saleReturnObserver,
        KasbonObserver $kasbonObserver,
        SalaryObserver $salaryObserver,
        CashIntOutObserver $cashIntOutObserver,
        TransactionDueObserver $transactionDueObserver
    ) {
        $this->activityLog          = $activityLogObserver;
        $this->variationObserver    = $variationObserver;
        $this->stockObserver        = $stockObserver;
        $this->salesObserver        = $salesObserver;
        $this->saleReturnObserver   = $saleReturnObserver;
        $this->kasbonObserver       = $kasbonObserver;
        $this->salaryObserver       = $salaryObserver;
        $this->cashIntOutObserver   = $cashIntOutObserver;
        $this->transactionDueObserver   = $transactionDueObserver;
    }


    public function activityLog(Request $request)
    {
        $logs       = $this->activityLog->logsData($request)->limit(20)->get(['created_at', 'causer_id', 'event', 'description']);
        return response()->json([
            'user'   => array(
                'id'        => $request->user ?? '',
                'name'      => $request->user_name ?? 'Semua'
            ),
            'list'    => ActivityLogResource::collection($logs),
        ], 200);
    }

    public function stockAlert(Request $request)
    {
        $products   = $this->stockObserver->getAlertQty($request)->limit(20)->get(['product_id', 'qty_available']);
        return response()->json(StockAlertResource::collection($products), 200);
    }

    public function profitCost(Request $request)
    {
        $settings = AccountSetting::first(['product_sale', 'product_retur_sale', 'product_discount_sale', 'discount_sale', 'product_cost', 'beban_operasional', 'beban_lainnya', 'pendapatan_lainnya', 'tax_output']);

        if (!$settings) {
            return response()->json([
                'pendapatan'    => 0.0,
                'pengeluaran'   => 0.0,
                'hpp'           => 0.0,
                'profit'        => 0.0
            ], 200);
        }

        $currentYear = date('Y');

        $penjualan = $settings->product_sale_account
            ? (float)$settings->product_sale_account->sheet()->whereYear('operation_date', $currentYear)->where(function ($q) {
                return $q->whereHas('sell.product', function ($q) {
                    return $q->where('is_stock', 'yes');
                });
            })->sum('amount')
            : 0.0;

        $jasa = $settings->product_sale_account
            ? (float)$settings->product_sale_account->sheet()->whereYear('operation_date', $currentYear)->where(function ($q) {
                return $q->whereHas('sell.product', function ($q) {
                    return $q->where('is_stock', 'no');
                });
            })->sum('amount')
            : 0.0;

        $discount = $settings->discount_account
            ? (float)$settings->discount_account->sheet()->whereYear('operation_date', $currentYear)->where(function ($q) {
                return $q->whereHas('transaction', function ($q) {
                    return $q->where("type", 'sell');
                });
            })->sum('amount')
            : 0.0;

        $return = $settings->product_retur_sale_account
            ? (float)$settings->product_retur_sale_account->sheet()->whereYear('operation_date', $currentYear)->sum('amount')
            : 0.0;

        $cogsDebit = $settings->product_cost_account
            ? (float)$settings->product_cost_account->sheet()->whereYear('operation_date', $currentYear)->where('type', 'debit')->sum('amount')
            : 0.0;

        $cogsCredit = $settings->product_cost_account
            ? (float)$settings->product_cost_account->sheet()->whereYear('operation_date', $currentYear)->where('type', 'credit')->sum('amount')
            : 0.0;

        $cogs = $cogsDebit - $cogsCredit;

        $jumlahBeban        = 0;
        $jumlahbebanlainnya = 0;
        $jumlahpendapatan   = 0;

        // Beban Operasional
        if ($settings->beban_operasional_account && $settings->beban_operasional_account->child) {
            foreach ($settings->beban_operasional_account->child as $operasional) {
                $amountData = (float)$operasional->sheet()->whereYear('operation_date', $currentYear)->sum('amount');
                $jumlahBeban += $amountData;
            }
        }

        // Beban Lainnya
        if ($settings->beban_lainnya_account && $settings->beban_lainnya_account->child) {
            foreach ($settings->beban_lainnya_account->child as $other) {
                $amountData = (float)$other->sheet()->whereYear('operation_date', $currentYear)->sum('amount');
                $jumlahbebanlainnya += $amountData;
            }
        }

        // Pendapatan Lainnya
        if ($settings->pendapatan_lainnya_account && $settings->pendapatan_lainnya_account->child) {
            foreach ($settings->pendapatan_lainnya_account->child as $pendapatan) {
                $amountData = (float)$pendapatan->sheet()->whereYear('operation_date', $currentYear)->sum('amount');
                $jumlahpendapatan += $amountData;
            }
        }

        return response()->json([
            'pendapatan'    => (float)((($penjualan + $jasa) - ($return + $discount)) + $jumlahpendapatan),
            'pengeluaran'   => (float)($jumlahbebanlainnya + $jumlahBeban),
            'hpp'           => (float)$cogs,
            'profit'        => (float)($penjualan + $jasa + $jumlahpendapatan) - ($return + $discount + $cogs + (float)$jumlahBeban + $jumlahbebanlainnya)
        ], 200);
    }

    public function customerDue(Request $request)
    {
        $dues       = $this->transactionDueObserver->getHutang($request);
        return response()->json(CustomerDueResource::collection($dues), 200);
    }

    public function activeUsers()
    {
        $activeUsers = User::where('last_active_at', '>=', Carbon::now()->subDays(1))->get(['name', 'last_active_at', 'photo']);
        return response()->json(UserActiveResource::collection($activeUsers));
    }

    public function topProducts(Request $request)
    {
        $timeFilter = $request->priode ?? 'day';

        $query = Sell::with(['variation', 'product', 'transaction'])
            ->selectRaw('sum(qty) as quantity, sum(unit_price * qty) as total, variation_id, store_id as store');

        if ($timeFilter === 'day') {
            $query->whereHas('transaction', function ($q) {
                $q->whereDate('created_at', Carbon::today());
            });
        } elseif ($timeFilter === 'month') {
            $query->whereHas('transaction', function ($q) {
                $q->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
            });
        } elseif ($timeFilter === 'year') {
            $query->whereHas('transaction', function ($q) {
                $q->whereYear('created_at', Carbon::now()->year);
            });
        }

        $data = $query->groupBy('variation_id', 'store')
            ->orderByDesc('quantity')
            ->limit(10)
            ->get();

        return response()->json(TopProductResource::collection($data));
    }

    public function topCustomers(Request $request)
    {
        $timeFilter = $request->priode ?? 'year';

        $query = Transaction::with(['customer'])->where('type', 'sell')
            ->selectRaw('sum(total_before_tax) as total_buy, customer_id');

        if ($timeFilter === 'day') {
            $query->whereDate('transaction_date', Carbon::today());
        } elseif ($timeFilter === 'month') {
            $query->whereMonth('transaction_date', Carbon::now()->month)
                ->whereYear('transaction_date', Carbon::now()->year);
        } elseif ($timeFilter === 'year') {
            $query->whereYear('transaction_date', Carbon::now()->year);
        }

        $data = $query->groupBy('customer_id')
            ->orderByDesc('total_buy')
            ->limit(10)
            ->get();

        return response()->json(TopCustomerResource::collection($data));
    }

    public function dailySale(Request $request)
    {
        $timeFilter     = $request->date ?? now()->format('Y-m-d');
        $transactions   = Transaction::selectRaw('sum(total_before_tax) as total_sell, count(id) as transactions')->whereDate('transaction_date', $timeFilter)->first();
        $sells          = Sell::whereHas('transaction', function ($q) use ($timeFilter) {
            return $q->whereDate('transaction_date', $timeFilter);
        })->selectRaw('count(variation_id) as typeproduct, sum(qty) as counter')->first();


        return response()->json([
            'amount'        => number_format($transactions->total_sell ?? 0),
            'transactions'  => number_format($transactions->transactions ?? 0),
            'type'          => number_format($sells->typeproduct ?? 0),
            'qty'           => number_format($sells->counter ?? 0),
            'date'          => $timeFilter
        ]);
    }

    public function rekonsiliasiBank()
    {
        $banks = AccountTransaction::whereHas('account', function ($q) {
            return $q->whereNotNull('bank_id');
        })
            ->selectRaw('
            account_id, 
        SUM(CASE 
            WHEN type = "debit" THEN amount 
            WHEN type = "credit" THEN -amount 
            ELSE 0 
        END) as total_unreconciled_amount,
         
        SUM(CASE 
            WHEN after_rekonsiliasi = "no" AND type = "debit" THEN amount 
            WHEN after_rekonsiliasi = "no" AND type = "credit" THEN -amount 
            ELSE 0 
        END) as total_reconciled_amount,

            COUNT(CASE WHEN after_rekonsiliasi = "no" THEN 1 END) as total_unreconciled_count,
            COUNT(CASE WHEN after_rekonsiliasi = "yes" THEN 1 END) as total_reconciled_count,
            SUM(amount) as total_amount,
            (SUM(CASE WHEN after_rekonsiliasi = "yes" THEN 1 ELSE 0 END) / SUM(amount)) * 100 as reconciled_percentage,
            (SUM(CASE WHEN after_rekonsiliasi = "no" THEN 1 ELSE 0 END) / SUM(amount)) * 100 as unreconciled_percentage
        ')
            ->groupBy('account_id')
            ->having('total_unreconciled_amount', '>', 0)
            ->get();

        return response()->json(BankRekonsiliationResource::collection($banks));
    }

    public function monthlyAnalisis(Request $request)
    {
        $month          = $request->date ? $request->date : now()->format('Y-m');
        $startOfMonth   = Carbon::parse($month)->startOfMonth()->toDateString();
        $endOfMonth     = Carbon::parse($month)->endOfMonth()->toDateString();
        $data['date']   = array();
        $data['amount'] = array();

        $salesData = DB::table('sell_purchases')
            ->join('sells', 'sell_purchases.sell_id', '=', 'sells.id')
            ->join('transactions', 'sells.transaction_id', '=', 'transactions.id')
            ->selectRaw('DATE(transactions.transaction_date) as date, SUM((sells.unit_price - sell_purchases.purchase_price) * sell_purchases.qty) as net_sales')
            ->where('transactions.store_id', my_store())
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                return $q->whereBetween('transactions.transaction_date', [$startOfMonth, now()->parse($endOfMonth)->addDay()]); 
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($salesData as $sales) {
            $date   =  $sales->date;
            $amount = (float)$sales->net_sales;
            array_push($data['date'], $date);
            array_push($data['amount'], $amount);
        }


        return response()->json([
            'data'      => $data,
            'date'      => $month
        ]);
    }

    public function dataPiutang(Request $request)
    {
        $month          = now()->format('Y-m');
        $startOfMonth   = Carbon::parse($month)->startOfMonth()->toDateString();
        $endOfMonth     = Carbon::parse($month)->endOfMonth()->toDateString();
        $dueData = TransactionDue::where('type', 'hutang')
            ->selectRaw('
            SUM(amount) as total_revenue,
            SUM(CASE WHEN total_due_amount = 0 THEN amount ELSE 0 END) as total_paid_invoices,
            SUM(CASE WHEN total_due_amount > 0 THEN total_due_amount ELSE 0 END) as total_unpaid_invoices,
            SUM(CASE WHEN total_due_amount > 0 AND due_end > ? THEN total_due_amount ELSE 0 END) as total_not_due_yet,
            SUM(CASE WHEN total_due_amount > 0 AND due_end <= ? THEN total_due_amount ELSE 0 END) as total_overdue
        ', [now()->format('Y-m-d H:i:s'), now()->format('Y-m-d H:i:s')])
            ->where(function ($q) use ($request) {
                if ($request->type == 'customer') {
                    $q->whereNotNull('customer_id')
                        ->whereHas('customer', function ($query) {
                            $query->where('store_id', my_store());
                        });
                } else {
                    $q->whereNotNull('supplier_id')
                        ->whereHas('supplier', function ($query) {
                            $query->where('store_id', my_store());
                        });
                }
            })
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                return $q->where('created_at', '>=', $startOfMonth)
                    ->where('created_at', '<=', $endOfMonth)->orWhere('total_due_amount','>',0);
            })
            ->first();

        return response()->json([
            'revenue'   => number_format($dueData->total_revenue),
            'paid'      => number_format($dueData->total_paid_invoices),
            'unpaid'    => number_format($dueData->total_unpaid_invoices),
            'not_due'   => number_format($dueData->total_not_due_yet),
            'overdue'   => number_format($dueData->total_overdue)
        ]);
    }
}
