<?php

namespace Poshub\Ecommerce\Models;

use App\Models\Admin\Store;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceApiSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rajaongkir',
        'merchant_id',
        'client_key',
        'server_key',
        'sub_district_id',
        'about_title',
        'copyright',
        'about_text',
        'about_image',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'youtube_url',
        'payment_method',
        'price_per_km',
        'kurir_manual',
        'store_id',
        'domain_site',
        'ecommerce_activation',
        'show_stock',
        'with_stock'
    ];


    public function subdistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_district_id');
    }

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores());

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class,'store_id');
    }
}
