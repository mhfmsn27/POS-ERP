<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakturPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_due_id',
        'transaction_id',
        'pay_amount',
        'status',
    ];


    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'transaction_id');
    }

    public function transaction_due()
    {
        return $this->belongsTo(TransactionDue::class,'transaction_due_id');
    }

    public function payment()
    {
        return $this->hasMany(TransactionPayment::class,'faktur_detail_id');
    }
}
