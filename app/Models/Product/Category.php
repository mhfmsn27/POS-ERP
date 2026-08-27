<?php

namespace App\Models\Product;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded  = ['id'];
    public $table    = 'categories';

    protected $fillable = [
        'id',
        'name',
        'detail',
        'is_root_parent',
        'parent_id',
        'show_in_ecommerce',
        'featured_category',
        'image'
    ];


    protected $casts = [
        'is_root_parent' => 'boolean'
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
            ->useLogName('category_product');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();
        
        if ($eventName == 'created') {
            $activity->description = "Penambahan Kategori Produk " . $activity->subject->name ?? '';
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Kategori Produk " . $activity->subject->name ?? '';
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Kategori Produk " . $activity->subject->name ?? '';
        }
    }

    /**
     * Get category childs
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get category parents
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get product 
     */
    public function product()
    {
        return $this->hasMany(Product::class, 'category_id');
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
