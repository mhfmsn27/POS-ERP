<?php

namespace App\Models\Transaction;

use App\Models\Account\AccountTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'method',
        'transaction_id',
        'payment_method_id',
        'created_by',
        'transaction_type',
        'account_id',
        'transaction_due_id',
        'date',
        'amount',
        'note',
        'faktur_detail_id',
        'order_id',
        'payment_status',
        'no_rek',
        'bank_name',
        'to_bank',
        'file'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(AccountTransaction::class, 'account_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id')->withTrashed();
    }

    public function due_payment()
    {
        return $this->belongsTo(TransactionDue::class, 'transaction_due_id');
    }

    public function payment_account()
    {
        return $this->hasMany(AccountTransaction::class, 'transaction_payment_id');
    } 
}
