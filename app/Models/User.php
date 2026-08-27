<?php

namespace App\Models;

use App\Models\Admin\Merchant;
use App\Models\Admin\Store;
use App\Models\Crm\SalesCommission;
use App\Models\Hrm\Employee;
use App\Models\Scopes\FilterByMerchant;
use App\Models\Transaction\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LaravelAndVueJS\Traits\LaravelPermissionToVueJS;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, LaravelPermissionToVueJS, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'photo',
        'phone',
        'jk',
        'is_sell',
        'commission_percentase',
        'max_commission',
        'merchant_id',
        'role_type',
        'store_id',
        'status',
        'password',
        'email_verified_at',
        'last_active_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = [
        'two_factor_expires_at',
        'last_active_at'
    ];

    protected static function booted()
    {

        if (auth()->guard('web')->check()) {   
            static::addGlobalScope(new FilterByMerchant);
        }

        static::creating(function ($model) { 
            $user = auth()->user();
            if ($user && $user->merchant_id != null) {
                $model->merchant_id = $user->merchant_id;
            }
        }); 
    }

    /**
     * Generate Two Factor Code For Verify Email
     * 
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = substr(rand(100000, 999999), 0, 6);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

     /**
     * Riset Two Factor Code For Verify Email
     * 
     */
    public function resetTwoFactorCode()
    {
        $this->timestamps = false;
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withTrashed();
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function role_data()
    {
        return $this->belongsTo(Role::class,'role');
    }

    public function commission()
    {
        return $this->hasMany(SalesCommission::class, 'commission_contact_id')->where("commission_contact_type", "user");
    }

    public function sales_transaction()
    {
        return $this->hasMany(Transaction::class, 'commission_contact_id')->where("type", "sell")->where("payment_status", "paid");
    }

    public function getTotalCommissionAttribute()
    {
        $totalCommission = $this->commission()->where("status", "due")->get()->sum('commission_total') - $this->commission()->where("status", "due")->get()->sum('commission_total_return');
        return $totalCommission;
    }

    public function total_commission($startDate, $endDate)
    {
        $totalCommission = $this->commission()->where(function ($q) use ($startDate, $endDate) {
            if ($endDate && $startDate) {
                return $q->whereDate("created_at", ">=", $startDate)->whereDate("created_at", "<=", $endDate);
            } else {
                return $startDate ? $q->whereDate("created_at", $startDate) : "";
            }
        })->whereHas('transaction', function ($q) {
            return $q->where("payment_status", "paid");
        })->sum('commission_total');

        return (float)$totalCommission;
    }

    public function total_transaction($startDate, $endDate)
    {
        $totalCommission = $this->sales_transaction()->where(function ($q) use ($startDate, $endDate) {
            if ($endDate && $startDate) {
                return $q->whereDate("created_at", ">=", $startDate)->whereDate("created_at", "<=", $endDate);
            } else {
                return $startDate ? $q->whereDate("created_at", $startDate) : "";
            }
        })->sum('total_before_tax');

        return (float)$totalCommission;
    }

    public function getImageDataAttribute()
    {
        if (file_exists($this->photo)) {
            return $this->photo;
        } else {
            return 'uploads/image-default.jpeg';
        }
    }
}
