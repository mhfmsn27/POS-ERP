<?php

namespace App\Models\Product;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class Rak extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded  = ['id'];

    protected $fillable = [
        'id',
        'floor',
        'room',
        'rak'
    ];

    /**
     * Log Activity
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('etalase');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();
        
        if ($eventName == 'created') {
            $activity->description = "Penambahan Etalase Rak  ";
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Etalase Rak ";
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Etalase Rak  ";
        }
    }

    // End Log Activity

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }
}
