<?php

namespace App\Models\Admin;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ModuleFeature extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
        'description', 
    ];

    protected static function booted()
    { 
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->store_id =  Uuid::uuid4()->toString();
            }
        });
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class,'module_id');
    }
}
