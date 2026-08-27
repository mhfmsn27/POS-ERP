<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PluginMenu extends Model
{
    use HasFactory;

    public function plugin()
    {
        return $this->belongsTo(Plugins::class,'plugin_id')->withTrashed();
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class,'permission_id');
    }
}
