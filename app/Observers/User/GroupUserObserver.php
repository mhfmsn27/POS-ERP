<?php

namespace App\Observers\User;

use App\Models\Admin\ModuleFeature;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupUserObserver
{

    public function getModules(Request $request)
    {
        return ModuleFeature::where(function ($query) use ($request) {
            return $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy("name", "asc");
    }

    public function getData(Request $request)
    {
        return Role::where(function ($query) use ($request) {
            return $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy("name", "asc");
    }

    public function getPermission(Request $request)
    {
        return Permission::where(function ($query) use ($request) {
            return $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($query) use ($request) {
            return $request->module ? $query->where('module_id',$request->module) : '';
        })->orderBy("name", "asc");
    }

    public function createData(Request $request)
    {
        return Role::create([
            'name'          => $request->name,
            'guard_name'    => 'web'
        ]);
    }

    public function updateData(Request $request, Role $role)
    {
        $role->update([
            'name'      => $request->name,
        ]);
    }

    public function deleteData(Role $role)
    {
        DB::table('role_has_permissions')
            ->where('role_id', $role)
            ->delete();

        $role->delete();
    }
}
