<?php

namespace App\Models\Account;

use App\Models\Admin\Store;
use App\Models\Crm\SalesCommissionAgent;
use App\Models\Hrm\Employee;
use App\Models\Scopes\FilterByStores;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\TransactionPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded          = [];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id')->withTrashed();
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withTrashed();
    }

    public function payment()
    {
        return $this->hasOne(TransactionPayment::class, 'transaction_id')->where("transaction_type", "expense");
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'contact_id');
    }

    public function agent()
    {
        return $this->belongsTo(SalesCommissionAgent::class, 'contact_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Employee::class, 'contact_id');
    }

    public function account_transaction()
    {
        return $this->hasMany(AccountTransaction::class,'expense_id');
    }

    public function getContactNameAttribute()
    {
        $name = '';
        if ($this->contact_type == 'user' || $this->contact_type == 'none') {
            $name = $this->user->name ?? '';
        } else if ($this->contact_type == 'agent') {
            $name = $this->agent->name ?? '';
        } else if ($this->contact_type == 'employee') {
            $name = $this->pegawai->user->name ?? '';
        }

        return $name;
    }

    public function list()
    {
        return $this->hasMany(ExpenseDetail::class, 'expense_id');
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class,'method_id');
    }
}
