<?php

namespace App\Models\Transaction;

use App\Models\Admin\Store;
use App\Models\Scopes\FilterByStores;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShiftRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function transactionshift()
    {
        return $this->hasMany(ShiftRegisterTransaction::class, 'shift_register_id');
    }

    public function saletransaction()
    {
        return $this->hasMany(ShiftRegisterTransaction::class,'shift_register_id')->where('transaction_type','sell');
    }

    public function returnsale()
    {
        return $this->hasMany(ShiftRegisterTransaction::class,'shift_register_id')->where('transaction_type','refund');
    }

    public function expenses()
    {
        return $this->hasMany(ShiftRegisterTransaction::class,'shift_register_id')->where('transaction_type','expense');
    }

    public function getOpeningShiftAttribute()
    {
        $total = $this->transactionshift()->where("shift_register_id", $this->id)->where("transaction_type", "opening")->get()->sum("amount");
        return $total;
    }

    public function getSellCashTransactionAttribute()
    {
        $total = $this->transactionshift()
            ->where("shift_register_id", $this->id)
            ->where("transaction_type", "sell")
            ->where("pay_method", "cash")
            ->get()->sum("amount");
        return $total;
    }

    public function getSellBankTransactionAttribute()
    {
        $total = $this->transactionshift()
            ->where("shift_register_id", $this->id)
            ->where("transaction_type", "sell")
            ->where("pay_method", "bank")
            ->get()->sum("amount");
        return $total;
    }

    public function getSellOtherTransactionAttribute()
    {
        $total = $this->transactionshift()
            ->where("shift_register_id", $this->id)
            ->where("transaction_type", "sell")
            ->where("pay_method", "bank")
            ->get()->sum("amount");
        return $total;
    }

    public function getExpenseTransactionAttribute()
    {
        $total = $this->transactionshift()
            ->where("shift_register_id", $this->id)
            ->where("transaction_type", "expense") 
            ->get()->sum("amount");
        return $total;
    }

    public function getReturnTransactionAttribute()
    {
        $total = $this->transactionshift()
            ->where("shift_register_id", $this->id)
            ->where("transaction_type", "refund") 
            ->get()->sum("amount");
        return $total;
    }

    public function getCashInHandAttribute()
    {
        $opening    = $this->getOpeningShiftAttribute();
        $refund     = $this->getReturnTransactionAttribute();
        $cash       = $this->getSellCashTransactionAttribute();
        $expense    = $this->getExpenseTransactionAttribute();

        $total = ($opening + $cash) - ($expense + $refund);
        return $total;
    }
     
}
