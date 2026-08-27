<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDetail extends Model
{
    use HasFactory;

    protected $guarded          = [];

    public function expense()
    {
        return $this->belongsTo(Expense::class,'expense_id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id')->withTrashed();
    }
}
