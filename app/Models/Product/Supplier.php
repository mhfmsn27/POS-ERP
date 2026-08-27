<?php

namespace App\Models\Product;

use App\Models\Account\Account;
use App\Models\Admin\Store;
use App\Models\Admin\TermPayment;
use App\Models\Scopes\FilterByStores;
use App\Models\Transaction\TransactionDue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_account',
        'term_payment',
        'debt',
        'debt_imprest',
        'detail',
        'tax_default',
        'npwp',
        'tax_option'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    /**
     * Log Activity
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('supplier');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();
        
        if ($eventName == 'created') {
            $activity->description = "Penambahan Supplier " . $activity->subject->name ?? '';
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Supplier " . $activity->subject->name ?? '';
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Supplier  " . $activity->subject->name ?? '';
        }
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }


    public function term()
    {
        return $this->belongsTo(TermPayment::class, 'term_payment')->withTrashed();
    }

    public function debt_account()
    {
        return $this->belongsTo(Account::class, 'debt')->withTrashed();
    }

    public function debt_imprest_account()
    {
        return $this->belongsTo(Account::class, 'debt_imprest')->withTrashed();
    }

    public function transaction_history()
    {
        return $this->hasMany(TransactionDue::class,'supplier_id');
    }

    public function due_history()
    {
        return $this->hasMany(TransactionDue::class, 'supplier_id')->where("type", "hutang");
    }

    public function saldo_history()
    {
        return $this->hasMany(TransactionDue::class, 'supplier_id')->where("type", "saldo");
    }

    public function getTotalDueAttribute()
    {

        $total = $this->due_history()->where('status', 'due')->sum('total_due_amount');
        return (int)$total;
    }

    public function getTotalSaldoAttribute()
    {
        $total = $this->saldo_history()->where("status", "due")->sum("total_due_amount");
        return (int)$total;
    }

    public function total_due_umur($umur = null)
    {
        if ($umur != null) {
            $total = $this->due_history()->where('status', 'due')->where(function ($q) use ($umur) {
                $q->whereRaw('DATEDIFF(NOW(), created_at) >= ?', [$umur]);
            })->sum('total_due_amount');
            return (int)$total;
        } else { 
            return $this->getTotalDueAttribute();
        }
    }
    

    public function total_saldo_umur($umur = null)
    {
        if ($umur != null) {
            $total = $this->saldo_history()->where('status', 'due')->where(function ($q) use ($umur) {
                $q->whereRaw('DATEDIFF(NOW(), created_at) >= ?', [$umur]);
            })->sum('total_due_amount');
            return (int)$total;
        } else {
            return $this->getTotalSaldoAttribute();
        }
    }
}
