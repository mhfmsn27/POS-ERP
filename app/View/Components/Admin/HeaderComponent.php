<?php

namespace App\View\Components\Admin;

use App\Models\Admin\Setting;
use App\Models\Admin\Store;
use App\Models\Hrm\Attendance;
use App\Models\Product\Stock;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Component;

class HeaderComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $lang = array(
            'id'    => __('indonesian'),
            'en'    => __('english'),
            'chn'   => __('china'),
            // 'ar'    => __('arabic')
        );
        $storeSettings = Store::findOrFail(Session::get('mystore'));
        $currency   = $storeSettings->currency->symbol ?? '';
        $settings   = Setting::first();
        $attendance = Attendance::where('date', date('Y-m-d'))->where('user_id', Auth()->user()->id)->first();

        $our = Stock::with("product", "variation")->where("store_id", Session::get('mystore'))->where(function ($q) {
            $q->whereHas('product', function ($query) {
                return  $query->whereRaw("alert_quantity >= stocks.qty_available");
            });
        });
        $totalStock = $our->count();
        $listStock = $our->limit(5)->get();
        $product = array();
        foreach ($listStock as $d) {
            if ($d->product->alert_quantity >= $d->qty_available) {
                if ($d->variation->name != 'no-name') {
                    $name = $d->product->name . ' - ' . $d->variation->name;
                } else {
                    $name = $d->product->name;
                }
                $list = [
                    'name'  => $name,
                    'store' => $d->store->name ?? '',
                    'stock' => (int)$d->qty_available,
                    'image' => asset($d->variation->gambar->path ?? '/uploads/image.jpg'),
                ];
                array_push($product, $list);
            }
        }
        return view('components.admin.header-component', compact('lang', 'currency', 'settings', 'attendance', 'totalStock', 'product', 'storeSettings'));
    }
}
