<?php

namespace App\Models\Product;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded  = ['id'];

    protected $fillable = [
        'id',
        'name',
        'code',
        'value',
        'is_root_parent',
        'parent_id', 
        'operator',
        'price',
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
            ->logOnly(['name', 'value', 'parent_id'])
            ->useLogName('unit_product');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();
        
        if ($eventName == 'created') {
            $activity->description = "Penambahan Unit Produk " . $activity->subject->name ?? '';
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Unit Produk " . $activity->subject->name ?? '';
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Unit Produk " . $activity->subject->name ?? '';
        }
    }

    // End Log Activity

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'parent_id')->withTrashed();
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }

    public function unitdown()
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function getUnitTurunanAttribute()
    {
        $data = Unit::where("id", $this->id)->orWhere("parent_id", $this->id)->get(['id', 'name', 'value']);
        return $data;
    }
}
