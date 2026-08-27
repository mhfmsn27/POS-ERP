<?php

namespace App\Models\Hrm;

use App\Models\Account\AccountTransaction;
use App\Models\Admin\Store;
use App\Models\Scopes\FilterByStores;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\TransactionDue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeKasbon extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded  = [];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'method_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function transaction()
    {
        return $this->hasMany(AccountTransaction::class, 'kasbon_id');
    }

    public function due_data()
    {
        return $this->hasOne(TransactionDue::class, 'kasbon_id');
    }
}
