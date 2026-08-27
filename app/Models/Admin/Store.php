<?php

namespace App\Models\Admin;

use App\Models\Hrm\Employee;
use App\Models\Scopes\FilterByMerchant;
use App\Models\Transaction\TransactionPackage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Poshub\Ecommerce\Models\SubDistrict;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $guarded          = [];

    protected $dates = [
        'two_factor_expires_at',
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByMerchant);

        if (auth()->check()) {
            if (auth()->user()->merchant_id) {
                static::creating(function ($model) {
                    $model->merchant_id = auth()->user()->merchant_id;
                });
            }
        }
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


    public function employee()
    {
        return $this->hasMany(Employee::class, 'store_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id')->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Cuurency::class, 'currency_id')->withTrashed();
    }

    public function printer()
    {
        return $this->belongsTo(Printer::class, 'printer_id')->withTrashed();
    }

    public function subdistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_district_id');
    }

    public function package_transaction()
    {
        return $this->hasMany(TransactionPackage::class, 'store_id')->orderBy("created_at", "desc");
    }

    public function store_package()
    {
        return $this->hasOne(TransactionPackage::class, 'store_id')->where("status", "success")->where("end_date", ">=", now())->orderBy("created_at", "desc");
    }

    public function active_package()
    {
        return $this->hasMany(TransactionPackage::class, 'store_id')->where("status", "success")->where("end_date", ">=", now());
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }


    public function getTransactionPackagePendingAttribute()
    {
        $status = true;

        if (count($this->package_transaction->where("status", "pending")) > 0) {
            $status = false;
        }

        return $status;
    }
}
