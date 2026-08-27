<?php

namespace App\View\Components\pos;

use App\Models\Admin\Customer;
use App\Models\Admin\Store;
use App\Models\Crm\SalesCommissionAgent;
use App\Models\Hrm\Employee;
use App\Models\Product\Category;
use App\Models\User;
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
        $category = Category::where('is_root_parent', 1)->get();
        $customer = Customer::all();
        $store = Store::findOrFail(Session::get('mystore'));
        $agent_commisiion = null;
        if ($store->commission_system == 1) {
            if ($store->commission_type == 'agent') {
                $agent_commisiion = SalesCommissionAgent::orderBy("name", "asc")->get(['id', "name"]);
            } else if ($store->commission_type == 'user') {
                $agent_commisiion = User::orderBy("name", "asc")->get(['id', "name"]);
            } else if ($store->commission_type == 'employee') {
                $agent_commisiion = Employee::orderBy("id", "desc")->get();
            }
        }
        return view('components.pos.header-component', compact('category', 'customer', 'store', 'agent_commisiion'));
    }
}
