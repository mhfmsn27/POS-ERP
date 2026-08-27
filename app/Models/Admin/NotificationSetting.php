<?php

namespace App\Models\Admin;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $guarded          = [];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        if (auth()->check()) {
            static::creating(function ($model) {
                $model->store_id = my_store();
            });
        }
    }

    public function registration_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'user_register');
    }

    public function user_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'user_add');
    }

    public function store_tempate()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'add_store');
    }

    public function order_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'ecommerce_order');
    }

    public function payment_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'ecommerce_payment');
    }

    public function shipping_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'ecommerce_shipping');
    }

    public function received_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'ecommerce_received');
    }

    public function rma_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'rma_add');
    }

    public function rma_process_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'rma_progress');
    }

    public function package_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'package_buy');
    }

    public function payment_package_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'package_payment');
    }

    public function delete_store_template()
    {
        return $this->belongsTo(WhatsappTemplate::class, 'delete_store');
    }
}
