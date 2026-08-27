<?php

namespace App\Http\Controllers\Api\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Store;
use App\Observers\Master\UserObserver;
use App\Observers\UserTableObserver;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userObserver;
    protected $userTableObserver;

    public function __construct(UserObserver $userObserver, UserTableObserver $userTableObserver)
    {
        $this->userObserver         = $userObserver;
        $this->userTableObserver    = $userTableObserver;
    }

    public function tableOptions(Request $request)
    {
        $data   = $this->userTableObserver->getData($request->table);
        return response()->json([
            'options'      => $data ? explode(",", $data->view_option) : null,
        ]);
    }

    public function createOptions(Request $request)
    {

        $this->userTableObserver->createUpdate($request);
        return response()->json([
            'message'      => 'Success Create Data',
        ]);
    }


    public function simple(Request $request)
    {
        $data       = $this->userObserver->getData($request)->limit(20)->get(['id', 'name']);

        return response()->json([
            'users'      => $data,
        ]);
    }

    public function sign()
    {
        $data       = auth()->user();
        $store      = Store::find(my_store());

        if ($data->is_sell != 'yes') {
            return response()->json([
                'id'        => '',
                'name'      => ''
            ]);
        }

        return response()->json([
            'id'        => $data->id,
            'name'      => $data->name,
            'store'     => array(
                'name'      => $store->name,
                'address'   => $store->address,
                'phone'     => $store->phone
            )
        ]);
    } 
}
