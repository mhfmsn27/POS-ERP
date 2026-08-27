<?php

namespace App\Models\Transaction;

use App\Models\Account\AccountTransaction;
use App\Models\Account\JurnalUmum;
use App\Models\Admin\Courier;
use App\Models\Admin\Customer;
use App\Models\Admin\Store;
use App\Models\Admin\Warehouse;
use App\Models\Crm\SalesCommission;
use App\Models\Product\Supplier;
use App\Models\Product\VoucherClaim;
use App\Models\Scopes\FilterByStores;
use App\Models\Stock\StockAdjusmentDetail;
use App\Models\Stock\StockTransferDetail;
use App\Models\Tax\TaxNoRefDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Poshub\Ecommerce\Models\TransactionShippingDetail;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'no_ref',
        'customer_id',
        'commission_contact_id',
        'commission_contact_type',
        'commission_contact_total',
        'business_id',
        'shipping_charges',
        'additional_notes',
        'shipping_details',
        'store_id',
        'type',
        'status',
        'payment_status',
        'tranfer_parent',
        'created_by',
        'invoice_no',
        'ref_no',
        'transaction_date',
        'total_before_tax',
        'tax_amount',
        'final_total',
        'return_parent',
        'supplier_id',
        'supplier_ref',
        'tax_final',
        'discount_final',
        'discount_type',
        'discount_amount',
        'service_charge',
        'other_charges',
        'other_type',
        'service_payment',
        'service_final',
        'profit_sale',
        'from_to_store',
        'type_sell',
        'shipping_alocation',
        'goverment_tax',
        'service_tax',
        'warehouse_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'address',
        'courier_id',
        'no_tax'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {  
            if(my_store() != null) {
                $model->store_id = my_store();
            }
           
        });
    }

    public function status_payment($id)
    {
        $dt = Transaction::where('type', 'purchase_return')->where('id', $id)->first();
        if ($dt->payment_status == 'due') {
            if ($dt->type != 'purchase_return') {
                return 'Tunggakan';
            } else {
                return 'Piutang';
            }
        } else {
            return 'Lunas';
        }

        if ($dt->payment_status == 'due') {
            return 'Piutang';
        } else {
            return 'Lunas';
        }
    }

    /**
     * Log Activity
     */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('transaction');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $type = $activity->subject->type;
        $activity->log_name = $type;
        $activity->store_id = my_store();
        $transaction = '';
        if ($type == 'open_stock') {
            $transaction = "Stok Awal";
        } elseif ($type == "purchase") {
            $transaction = "Faktur Pembelian";
        } elseif ($type == "purchase_return") {
            $transaction = "Return Faktur Pembelian";
        } elseif ($type == "sales_return") {
            $transaction = "Return Faktur Penjualan";
        } elseif ($type == "sell") {
            $transaction = "Faktur Penjualan";
        } elseif ($type == "stock_adjustment") {
            $transaction = "Stok Opname";
        } elseif ($type == "purchase_payment") {
            $transaction = "Pembayaran Pembelian";
        } elseif ($type == "received_product") {
            $transaction = "Penerimaan Produk";
        } elseif ($type == "sales_payment") {
            $transaction = "Penerimaan Penjualan";
        } elseif ($type == "sell") {
            $transaction = "Faktur Penjualan";
        } elseif ($type == "shipping_product") {
            $transaction = "Pengiriman Produk";
        }

        if ($eventName == 'created') {
            $activity->description = "Pembuatan Transaksi " . $transaction;
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Transaksi " . $transaction;
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Transaksi " . $transaction;
        }
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withTrashed();
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class, 'courier_id');
    }

    public function transaction_due()
    {
        return $this->hasOne(TransactionDue::class, 'transaction_id');
    }

    public function sell_one()
    {
        return $this->hasOne(Sell::class, 'transaction_id')->orderBy('item_position','asc');
    }

    public function account_transaction()
    {
        return $this->hasMany(AccountTransaction::class, 'transaction_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withTrashed();
    }

    public function payment()
    {
        return $this->hasMany(TransactionPayment::class, 'transaction_id')->where("transaction_type", "transaction");
    }

    public function paycredit()
    {
        return $this->hasMany(TransactionPayment::class, 'transaction_id')->where("transaction_type", "transaction");
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'transaction_id');
    }

    public function purchase_return()
    {
        return $this->hasMany(Transaction::class, 'return_parent');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'return_parent');
    }

    public function returndetail()
    {
        return $this->hasMany(ReturnDetail::class, 'transaction_id');
    }

    public function sellreturn()
    {
        return $this->hasMany(SalesReturn::class, 'transaction_id');
    }

    public function transfer()
    {
        return $this->hasOne(StockTransferDetail::class, 'transaction_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function adjustment()
    {
        return $this->hasMany(StockAdjusmentDetail::class, 'transaction_id');
    }

    public function manytransfer()
    {
        return $this->hasMany(StockTransferDetail::class, 'transaction_id');
    }

    public function sell()
    {
        return $this->hasMany(Sell::class, 'transaction_id')->orderBy('item_position','asc');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sale()
    {
        return $this->hasMany(Sell::class, 'transaction_id')->orderBy('item_position','asc');
    }

    public function sales_return()
    {
        return $this->hasMany(Transaction::class, 'return_parent');
    }

    public function createdby()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function commission()
    {
        return $this->hasOne(SalesCommission::class, 'transaction_id');
    }

    public function voucher()
    {
        return $this->hasOne(VoucherClaim::class, 'transaction_id');
    }

    public function po()
    {
        return $this->hasMany(Purchase::class, 'po_id');
    }

    public function purchase_received()
    {
        return $this->hasMany(Purchase::class, 'transaction_received_id');
    }

    public function offer()
    {
        return $this->hasMany(Sell::class, 'offer_id');
    }

    public function sale_shipping()
    {
        return $this->hasMany(Sell::class, 'transaction_received_id')->orderBy('item_position','asc');
    }

    public function faktur_detail()
    {
        return $this->hasMany(FakturPaymentDetail::class, 'transaction_id');
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    public function shipping_detail()
    {
        return $this->hasOne(TransactionShippingDetail::class, 'transaction_id');
    }

    public function commission_user()
    {
        return $this->belongsTo(User::class, 'commission_contact_id');
    }

    public function from_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function to_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function no_tax_ref()
    {
        return $this->hasOne(TaxNoRefDetail::class,'transaction_id');
    }

    public function jurnal()
    {
        return $this->hasMany(JurnalUmum::class,'transaction_id');
    }

    public function getFinalTotalFakturAttribute()
    {
        $pay        = $this->final_total;
        $minues     = $this->faktur_detail()->whereHas('transaction_due', function ($q) {
            return $q->where("type", "saldo");
        })->sum("pay_amount");

        $total      = $pay - abs($minues);

        return $total;
    }

    public function getDueTotalAttribute()
    {
        $paySell    = $this->payment()->get()->sum('amount');
        $payRetur   = $this->sales_return()->get()->sum(function ($payment) {
            return $payment->payment()->get()->sum('amount');
        });
        $d          = $this->final_total;
        $r          = $this->sales_return()->get()->sum('final_total') - $payRetur;
        $due = $d - $r;
        $dueIs = $due - $paySell;
        return $dueIs;
    }

    public function getTotalUseModalAttribute()
    {
        return $this->sell()->get()->sum(function ($sell) {
            return $sell->total_modal_price;
        });
    }

    public function getSubtotalSellProductAttribute()
    {
        return $this->sell()->get()->sum(function ($sell) {
            return $sell->subtotal_after_retur;
        });
    }

    public function getSubtotalSellProductWithoutTaxAttribute()
    {
        return $this->sell()->get()->sum(function ($sell) {
            return $sell->subtotal_without_tax;
        });
    }

    public function getDueTotalPoAttribute()
    {
        $paySell    = $this->payment()->get()->sum('amount');
        $payRetur   = $this->purchase_return()->get()->sum(function ($payment) {
            return $payment->payment()->get()->sum('amount');
        });
        $d          = $this->final_total;
        $r          = $this->purchase_return()->get()->sum('final_total') - $payRetur;
        $due = $d - $r;
        $dueIs = $due - $paySell;
        return $dueIs;
    }

    public function getDueTotalReturnSellAttribute()
    {
        $firstTrancation = 0;
        $ownerPay = 0;
        $returnedQty = 0;

        if ($this->transaction != null) {
            $firstTrancation = $this->transaction->final_total;
            $ownerPay = $this->transaction->payment()->get()->sum("amount");
            $returnedQty = $this->transaction->sales_return()->where("payment_status", "paid")->where("id", "!=", $this->id)->get()->sum("final_total");
        }
        $myPay = $this->payment()->get()->sum("amount");
        $comeBack = ($myPay + $firstTrancation) - ($ownerPay + $returnedQty);
        $myDue = $this->final_total - $comeBack;

        if ($myDue <= 0) {
            $myDue = 0;
        }

        return $myDue;
    }

    public function getDueTotalReturnAttribute()
    {
        $firstTrancation = 0;
        $ownerPay = 0;
        $returnedQty = 0;

        if ($this->transaction != null) {
            $firstTrancation = $this->transaction->final_total;
            $ownerPay = $this->transaction->payment()->get()->sum("amount");
            $returnedQty = $this->transaction->purchase_return()->where("payment_status", "paid")->where("id", "!=", $this->id)->get()->sum("final_total");
        }
        $myPay = $this->payment()->get()->sum("amount");
        $comeBack = ($myPay + $firstTrancation) - ($ownerPay + $returnedQty);
        $myDue = $this->final_total - $comeBack;

        if ($myDue <= 0) {
            $myDue = 0;
        }

        return $myDue;
    }

    public function getTransferQtyAttribute()
    {
        $transfer = $this->manytransfer()->sum('transfer_qty');
        if ($transfer != null) {
            return $transfer;
        }
        return 0;
    }

    public function getAdjustmentQtyAttribute()
    {
        $adjustment = $this->adjustment()->sum('qty_adjustment');
        if ($adjustment != null) {
            return $adjustment;
        }

        return 0;
    }

    public function getPayTotalAttribute()
    {
        $payment = $this->payment()->get()->sum('amount');
        if ($payment != null) {
            return number_format($payment);
        }

        return 0;
    }

    public function getQtySellAttribute()
    {
        $sell = $this->sell()->get()->sum('qty');
        if ($sell != null) {
            return $sell;
        }

        return 0;
    }

    public function getQtyPurchaseAttribute()
    {
        $purchase = $this->purchase()->get()->sum('quantity');
        if ($purchase != null) {
            return $purchase;
        }

        return 0;
    }

    public function getQtyReturnAttribute()
    {
        $return = $this->returndetail()->get()->sum('return_qty');
        if ($return != null) {
            return $return;
        }

        return 0;
    }

    public function getReturnAttribute()
    {
        $itereturn = $this->purchase()->get()->sum('qty_return');
        if ($itereturn != 0) {
            echo '<span class=" badge bg-danger text-white">(' . $itereturn . ') Item Qty Returned</span>';
        }
        echo '';
    }


    public function getReturnSellAttribute()
    {
        $returnsell = $this->sell()->get()->sum('qty_return');
        if ($returnsell != 0) {
            echo '<span class=" badge bg-danger text-white">(' . $returnsell . ') Item Qty Returned</span>';
        }
        echo '';
    }

    public function getTotalPaymentAttribute()
    {
        $total = $this->payment()->get()->sum("amount");
        return $total;
    }

    public function getProfitAttribute()
    {
        $jumlah = DB::table("transactions as t")
            ->join('sells as s', 't.id', '=', 's.transaction_id')
            ->join('sell_purchases as sp', 's.id', '=', 'sp.sell_id')
            ->join('purchases as pp', 'sp.purchase_id', '=', 'pp.id')
            ->selectRaw("SUM(((s.qty - s.qty_return) * (s.unit_price_before_disc - (IFNULL(s.item_tax,0) / 100 * s.unit_price_before_disc))) - ((s.qty - s.qty_return) * pp.purchase_price) ) AS jumlah, 
            SUM(s.qty * pp.purchase_price) AS modal, SUM(s.qty * s.unit_price) AS harga_jual ")
            ->where("t.id", $this->id)
            ->first();
        return $jumlah->jumlah;
    }

    public function getPaymentCashAttribute()
    {
        $total = $this->payment()->where("transaction_id", $this->id)->sum("amount");
        return $total;
    }
}
