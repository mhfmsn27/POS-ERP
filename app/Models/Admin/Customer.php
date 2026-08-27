<?php

namespace App\Models\Admin;

use App\Models\Account\Account;
use App\Models\Scopes\FilterByStores;
use App\Models\Transaction\TransactionDue;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, SoftDeletes, Notifiable, LogsActivity;

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
        'verify_expire',
        'email_verify',
        'password',
        'tax_default',
        'npwp',
        'type',
        'tax_option',
        'store_id',
        'default'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->store_id = my_store();
            }
        });
    }

    /**
     * Log Activity
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('customer');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();

        if ($eventName == 'created') {
            $activity->description = "Penambahan Pelanggan " . $activity->subject->name ?? '';
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Pelanggan " . $activity->subject->name ?? '';
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Pelanggan " . $activity->subject->name ?? '';
        }
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
        return $this->hasMany(TransactionDue::class, 'customer_id');
    }

    public function due_history()
    {
        return $this->hasMany(TransactionDue::class, 'customer_id')->where("type", 'hutang');
    }

    public function saldo_history()
    {
        return $this->hasMany(TransactionDue::class, 'customer_id')->where("type", "saldo");
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verify' => 'datetime',
        'verify_expire' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->code_verify_email = substr(rand(100000, 999999), 0, 6);
        $this->verify_expire = now()->addMinutes(10);
        $this->save();
    }


    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->code_verify_email = null;
        $this->verify_expire = null;
        $this->save();
    }
}
