<?php

namespace App\Observers\Setting;

use App\Models\Admin\SettingsHrm;
use App\Models\Admin\Store;

class SettingObserver
{
    public function createHrmDefault(Store $store)
    {
        return SettingsHrm::create([
            'merchant_id'       => auth()->user()->merchant_id,
            'store_id'          => $store->id,
            'salary_tax'        => 0,
        ]);
    }
}
