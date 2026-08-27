<?php

namespace App\Models\Account;

use App\Models\Admin\Bank;
use App\Models\Admin\Store;
use App\Models\Product\Product;
use App\Models\Scopes\FilterByStores;
use App\Models\Transaction\SmartlinkBank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'coa',
        'store_id',
        'account_type_id',
        'created_by',
        'closed_date',
        'closed',
        'note',
        'edit_option',
        'default_data',
        'is_root_parent',
        'parent_id',
        'bank_id',
        'cashflow',
        'type_account',
        'end_balance'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            if (my_store() != null) {
                $model->store_id = my_store();
            }
        });
    }

    public function smartlink()
    {
        return $this->hasOne(SmartlinkBank::class, 'account_id');
    }

    public function type()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id')->withTrashed();
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function sheet()
    {
        return $this->hasMany(AccountTransaction::class, 'account_id');
    }

    public function sheet_one()
    {
        return $this->hasOne(AccountTransaction::class, 'account_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function supply()
    {
        return $this->hasMany(Product::class, 'supply')->withTrashed();
    }

    public function sale()
    {
        return $this->hasMany(Product::class, 'sale')->withTrashed();
    }

    public function return_sale()
    {
        return $this->hasMany(Product::class, 'retur_sale')->withTrashed();
    }

    public function discount()
    {
        return $this->hasMany(Product::class, 'discount_sale')->withTrashed();
    }

    public function sent()
    {
        return $this->hasMany(Product::class, 'sent')->withTrashed();
    }

    public function cost()
    {
        return $this->hasMany(Product::class, 'cost')->withTrashed();
    }

    public function retur_purchase()
    {
        return $this->hasMany(Product::class, 'retur_purchase')->withTrashed();
    }

    public function supplier_debt()
    {
        return $this->hasMany(Product::class, 'supplier_debt')->withTrashed();
    }

    public function balance_date(String $startDate, String $endDate, String $inventory, String $transactionType = '', String $type = '', String $year = '', String $month = '')
    {
        return $this->sheet()->where(function ($q) use ($type) {
            return $type != '' ? $q->where("type", $type) : '';
        })->where(function ($q) use ($startDate, $endDate) {
            if ($startDate != '' && $endDate != '') {
                return $q->whereBetween('operation_date', [$startDate, $endDate]);
            } else if ($startDate != '' && $endDate == '') {
                return $startDate != '' ? $q->whereDate("operation_date", $startDate) : "";
            } else if ($endDate != '' && $startDate == '') {
                return $startDate != '' ? $q->whereDate("operation_date", "<=", $endDate) : "";
            }
        })->where(function ($q) use ($year, $month) {
            return $year != '' && $month != '' ? $q->whereYear('operation_date', $year)->whereMonth('operation_date', $month) : '';
        })->where(function ($q) use ($inventory) {
            return $inventory != '' ? $q->whereHas('sell.product', function ($q) use ($inventory) {
                return $q->where('is_stock', $inventory);
            }) : '';
        })->where(function ($q) use ($transactionType) {
            return $transactionType != '' ? $q->whereHas('transaction', function ($q) use ($transactionType) {
                return $q->where("type", $transactionType);
            }) : '';
        })->sum('amount');
    }

    public function cogs_sell(String $startDate, String $endDate)
    {
        return $this->sheet()->where("sub_type", "sale_faktur")->where(function ($q) use ($startDate, $endDate) {
            if ($startDate != '' && $endDate != '') {
                $q->whereBetween('operation_date', [$startDate, $endDate]);
            } else {
                $startDate != '' ? $q->whereDate("operation_date", $startDate) : "";
            }
        })->get()->sum(function ($sell) {
            return $sell->sell()->selectRaw("(sum(purchase_price) * (qty - qty_return)) as jumlah")->first()->jumlah;
        });
    }

    public function getTypeNameAttribute()
    {
        $name = '';
        if ($this->type->parent_type_id == null) {
            $name = $this->type->name ?? '';
        } else {
            $name = $this->type->parent_type->name ?? '';
        }

        return $name;
    }

    public function getSubTypeNameAttribute()
    {
        $name = '';
        if ($this->type->parent_type_id != null) {
            $name = $this->type->name ?? '';
        }

        return $name;
    }

    public function getBalanceAccountAttribute()
    {
        $debit      = $this->sheet()->where("deleted_at", null)->where("type", "debit")->sum("amount");
        $credit     = $this->sheet()->where("deleted_at", null)->where("type", "credit")->sum("amount");
        $balance    = $debit - $credit;

        return $balance;
    }

    public function balance_account_by_date($date)
    {
        $debit = $this->sheet()->where("type", "debit")->whereDate("operation_date", "<=", $date)->sum("amount");
        $credit = $this->sheet()->where("type", "credit")->whereDate("operation_date", "<=", $date)->sum("amount");

        $balance = $debit - $credit;



        return $balance;
    }

    public function debit_account_by_date($date)
    {
        $debit = $this->sheet()->where("type", "debit")->whereDate("operation_date", "<=", $date)->sum("amount");
        return $debit;
    }

    public function credit_account_by_date($date)
    {
        $credit = $this->sheet()->where("type", "credit")->whereDate("operation_date", "<=", $date)->sum("amount");
        return $credit;
    }

    public function getCashFlowDataAttribute()
    {
        return $this->calculateCashFlow($this);
    }

    private function calculateCashFlow($account)
    {
        if ($account->is_root_parent == 'yes') {
            return (float)$account->cashflow;
        } else {
            $totalCashFlow = 0;

            foreach ($account->child as $child) {
                $totalCashFlow += $this->calculateCashFlow($child);
            }

            $totalCashFlow += (float)$account->cashflow;

            return $totalCashFlow;
        }
    }

    public function total_by_type($startDate, $endDate, $type)
    {
        return $this->sheet()->where(function ($q) use ($startDate, $endDate) {
            if ($endDate && $startDate) {
                return $q->whereBetween('operation_date', [$startDate, $endDate]);
            } else {
                return $startDate ? $q->whereDate("operation_date", $startDate) : "";
            }
        })->where("type", $type)->sum('amount');
    }
}
