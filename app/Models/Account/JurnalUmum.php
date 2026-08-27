<?php

namespace App\Models\Account;

use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalUmum extends Model
{
    use HasFactory;

    protected $guarded          = [];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'transaction_id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id')->withTrashed();
    }
}
