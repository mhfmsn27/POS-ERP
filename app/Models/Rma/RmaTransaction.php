<?php

namespace App\Models\Rma;

use App\Models\Admin\Customer;
use App\Models\Admin\Store;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class RmaTransaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'store_id',
        'customer_id',
        'invoice',
        'ref_no',
        'note',
        'estimate_date',
        'estimate_price',
        'price',
        'status',
        'phone',
        'customer_name'
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
            ->useLogName('rma');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();

        if ($eventName == 'created') {
            $activity->description = "Penambahan Rma  " . $activity->subject->ref_no ?? '';
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Rma " . $activity->subject->ref_no ?? '';
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Rma  " . $activity->subject->ref_no ?? '';
        }
    }


    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }

    public function details()
    {
        return $this->hasMany(RmaDetail::class, 'rma_transactions_id');
    }

    public function getCompleteDetailAttribute()
    {
        return $this->details()->where('status', 'complete')->count();
    }

    public function getTakenDetailAttribute()
    {
        return $this->details()->where('status', 'taken')->count();
    }

    public function records()
    {
        return $this->hasMany(RmaRecord::class, 'rma_transactions_id');
    }
}
