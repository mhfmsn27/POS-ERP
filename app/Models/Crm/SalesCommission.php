<?php

namespace App\Models\Crm;

use App\Models\Account\Expense;
use App\Models\Hrm\Employee;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesCommission extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded  = [];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'commission_contact_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Employee::class, 'commission_contact_id');
    }

    public function agent()
    {
        return $this->belongsTo(SalesCommissionAgent::class, 'commission_contact_id');
    }

    public function expense()
    {
        return $this->hasMany(Expense::class, 'sales_commission_id');
    }

    public function getAgentNameAttribute()
    {
        $name = '';
        if ($this->commission_contact_type == 'user' || $this->commission_contact_type == 'none') {
            $name = $this->user->name ?? '';
        } else if ($this->commission_contact_type == 'pegawai') {
            $name = $this->pegawai->user->name ?? '';
        } else if ($this->commission_contact_type == 'agent') {
            $name = $this->agent->name ?? '';
        } else if ($this->commission_contact_type == 'employee') {
            $name = $this->pegawai->user->name ?? '';
        }

        return $name;
    }

    public function getStatusNameAttribute()
    {
        if ($this->status == 'due') {
            $name = "Hutang";
        } else {
            $name = "Lunas";
        }

        return $name;
    }

    public function getTypeNameAttribute()
    {
        if ($this->commission_contact_type == 'none') {
            $name = "Berdasarkan Login";
        } else if ($this->commission_contact_type == 'user') {
            $name = "User / Pengguna";
        } else if ($this->commission_contact_type == 'employee') {
            $name = "Pegawai";
        } else if ($this->commission_contact_type == 'agent') {
            $name = "Agent Penjualan";
        }

        return $name;
    }

    public function getDueTotalAttribute()
    {
        $payment = $this->expense()->get()->sum("amount");
        $due = $this->commission_total - $this->commission_total_return;
        $finalDue = $due - $payment;
        return $finalDue;
    }
}
