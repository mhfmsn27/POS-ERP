<?php

namespace App\Models\Transaction;

use App\Models\Account\Account;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'service',
        'amount',
        'account_id',
        'automatic_sync',
        'an',
        'no_rek',
        'logo',
        'store_id'
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
            ->useLogName('payment_method');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();
        if ($eventName == 'created') {
            $activity->description = "Penambahan Metode Pembayaran " . $activity->subject->name ?? '';
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Metode Pembayaran " . $activity->subject->name ?? '';
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Metode Pembayaran " . $activity->subject->name ?? '';
        }
    }



    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withTrashed();
    }


    public function payment()
    {
        return $this->hasMany(TransactionPayment::class, 'payment_method_id');
    }

    public function getImageDataAttribute()
    {
        if (file_exists($this->logo)) {
            return $this->logo;
        } else {
            return 'uploads/image-default.jpeg';
        }
    }
}
