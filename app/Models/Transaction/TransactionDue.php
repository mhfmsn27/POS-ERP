<?php

namespace App\Models\Transaction;

use App\Models\Account\AccountTransaction;
use App\Models\Admin\Customer;
use App\Models\Hrm\EmployeeKasbon;
use App\Models\Product\Supplier;
use App\Models\Salary\Salary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TransactionDue extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_ref',
        'transaction_id',
        'kasbon_id',
        'salary_id',
        'customer_id',
        'supplier_id',
        'amount',
        'note',
        'date',
        'status',
        'type',
        'due_end',
        'total_due_amount',
        'due_limit',

    ];


    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function account_transaction()
    {
        return $this->hasMany(AccountTransaction::class, 'transaction_due_id');
    }

    public function payment()
    {
        return $this->hasMany(TransactionPayment::class, 'transaction_due_id');
    }

    public function faktur()
    {
        return $this->hasMany(FakturPaymentDetail::class, 'transaction_due_id');
    }

    public function getTotalPaymentAttribute()
    {
        return $this->payment()->sum('amount');
    }

    public function kasbon()
    {
        return $this->belongsTo(EmployeeKasbon::class, 'kasbon_id');
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class, 'salary_id');
    }

    public function getTotalDueAttribute()
    {
        $totalDue       = (float)$this->amount - abs((float)$this->getTotalPaymentAttribute());
        return $totalDue;
    }

    public function getUmurAttribute()
    {
        // Parse the transaction date
        $createdDate = Carbon::parse($this->attributes['date']);

        // Get the current date
        $now = Carbon::now();

        // Calculate the difference in days
        $umur = $createdDate->diffInDays($now);

        // Return the age in days
        return $umur;
    }
}
