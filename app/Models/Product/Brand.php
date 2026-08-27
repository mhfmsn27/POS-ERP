<?php

namespace App\Models\Product;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Brand extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded  = ['id'];

    protected $fillable = [
        'id',
        'name',
        'code',
        'detail',
        'image',
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
            ->logOnly(['name'])
            ->useLogName('brand');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();
        
        if ($eventName == 'created') {
            $activity->description = "Penambahan Merk / Brand  " . $activity->subject->name ?? '';
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Merk / Brand " . $activity->subject->name ?? '';
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Merk / Brand  " . $activity->subject->name ?? '';
        }
    }

    public function getImageDataAttribute()
    {
        if (file_exists($this->image)) {
            return $this->image;
        } else {
            return 'uploads/image-default.jpeg';
        }
    }
}
