<?php

namespace App\Observers\Master;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class UserObserver
{
    public function getData(Request $request, $type = '')
    {
        return User::orderBy('name', 'asc')->where(function ($q) use ($request) {
            return $request->name ?  $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->type ? $q->where("is_sell", $request->type) : '';
        })->where(function ($q) use ($request) {
            return $request->user ? $q->where("id", $request->user) : '';
        })->where(function ($q) use ($type) {
            return $type != '' ? $q->where('role_type', $type) : '';
        });
    }

    public function createDataAdministrator(Request $request, String $image)
    {
        return User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'phone'         => $request->phone,
            'jk'            => $request->jk,
            'role_type'     => 'administrator',
            'photo'         => $image
        ]);
    }

    public function updateDataAdministrator(Request $request, User $user, String $image)
    {
        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'jk'            => $request->jk,
            'photo'         => $image == '' ? $user->photo : $image
        ]);
    }

    public function userRegister(Request $request, String $image = '')
    {
        return User::create([
            'name'      => $request->name,
            'commission_percentase'         => !empty($request->commission) ? $request->commission : 0, 
            'max_commission'                => !empty($request->commission) ? ($request->commission > 0 ? $request->max_commission : 0) : 0,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'jk'        => $request->jk,
            'phone'     => $request->phone,
            'photo'     => $image, 
        ]);
    }

    public function updateData(Request $request, User $user)
    {
        $user->update([
            'store_id'              => implode(',', array_column($request->stores, 'id')),
            'commission_percentase'         => !empty($request->commission) ? $request->commission : 0, 
            'max_commission'                => !empty($request->commission) ? ($request->commission > 0 ? $request->max_commission : 0) : 0,
            'name'                  => $request->name,
            'email'                 => $request->email,
            'phone'                 => $request->phone,  
            'role'                  => $request->role,
            'jk'                    => $request->jk,
        ]);

        $this->giveAccess($user, 'update');
        return $user;
    }

    public function giveAccess(User $user, String $type)
    {
        if ($type == 'update') {
            if ($user->role_data) {
                $user->roles()->detach();
                app(PermissionRegistrar::class)->forgetCachedPermissions();
            }
        }

        $getRole = Role::findOrFail($user->role);
        $getRole->give_model_has_role_data($user->id);
        $user->save();
    }
}
