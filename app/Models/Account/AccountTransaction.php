<?php

namespace App\Models\Account;

use App\Models\Scopes\FilterByStores;
use App\Models\Transaction\Sell;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionDue;
use App\Models\Transaction\TransactionPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AccountTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'created_by',
        'expense_id',
        'transaction_payment_id',
        'transaction_id',
        'transaction_transfer_id',
        'amount',
        'type',
        'sub_type',
        'ref_no',
        'operation_date',
        'salary_id',
        'kasbon_id',
        'note',
        'name',
        'after_rekonsiliasi',
        'account_transaction_id',
        'transaction_due_id',
        'item_id',
        'tax_paid',
        'tax_status',
        'spt_taxes_id',
        'qty_history',
        'older_amount',
        'older_qty_history',
        'adjust_account_id',
        'tax_gunggung',
        'tax_type',
        'cashflow'
    ];



    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withTrashed();
    }

    public function from_transfer()
    {
        return $this->hasOne(AccountTransaction::class, 'transaction_transfer_id')->withTrashed();
    }

    public function to_transfer()
    {
        return $this->hasOne(AccountTransaction::class, 'transaction_transfer_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function payment()
    {
        return $this->belongsTo(TransactionPayment::class, 'transaction_payment_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id')->withTrashed();
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id')->withTrashed();
    }

    public function account_transaction()
    {
        return $this->hasMany(AccountTransaction::class, 'account_transaction_id');
    }

    public function transaction_due()
    {
        return $this->belongsTo(TransactionDue::class, 'transaction_due_id');
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'item_id');
    }

    public function getBalanceSheetAttribute()
    {
        $jumlah = DB::table("account_transactions")
            ->selectRaw("SUM(IF(type='debit', amount, -1 * amount)) as balance")
            ->where("account_id", $this->account_id)
            ->where("deleted_at", null)
            ->where("operation_date", "<=", $this->operation_date)
            ->first();
        return $jumlah->balance;
    }

    public function getCashFlowPositionAttribute()
    {
        $cashFlow = AccountTransaction::where(function ($query) {
            $query->where("operation_date", "<", $this->operation_date)
                  ->orWhere(function ($subQuery) {
                      $subQuery->where("operation_date", "=", $this->operation_date)
                               ->where("id", "<", $this->id);
                  });
        })
        ->where("account_id", $this->account_id)
        ->orderBy("operation_date", 'desc')
        ->orderBy("id", 'desc')
        ->first(['cashflow', 'operation_date', 'id', 'transaction_id']);

       
        if ($cashFlow) {
            return $cashFlow->cashflow;
        }

        return 0;
    }

    public function getAmountDebitAttribute()
    {
        $amount = 0;
        if ($this->type == 'debit') {
            $amount = $this->amount;
        }

        return $amount;
    }

    public function getAmountCreditAttribute()
    {
        $amount = 0;
        if ($this->type == 'credit') {
            $amount = $this->amount;
        }

        return $amount;
    }

    public function getRouteNameAttribute()
    {
        if ($this->transaction) {
            if ($this->transaction->type == 'purchase_return') {
                return 'purchase_return_detail';
            }

            if ($this->transaction->type == 'purchase_payment') {
                return 'purchase_payment_update';
            }

            if ($this->transaction->type == 'purchase') {
                return 'purchase_detail';
            }

            if ($this->transaction->type == 'sales_payment') {
                return 'sales_payment_update';
            }

            if ($this->transaction->type == 'sell') {
                return 'sales_detail';
            }

            if ($this->transaction->type == 'shipping_product') {
                return 'sales_shipping_detail';
            }

            if ($this->transaction->type == 'received_product') {
                return 'purchase_received_detail';
            }

            if ($this->transaction->type == 'return_sell') {
                return 'sales_return_detail';
            }
        }

        return null;
    }
}
