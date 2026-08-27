<?php

namespace App\Models\Transaction;

use App\Models\Account\Expense;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftRegisterTransaction extends Model
{
    use HasFactory;

    public function shiftregister()
    {
        return $this->belongsTo(ShiftRegister::class,'shift_register_id')->withTrashed();
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'transaction_id')->withTrashed();
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class,'transaction_id')->withTrashed();
    }
 
}
