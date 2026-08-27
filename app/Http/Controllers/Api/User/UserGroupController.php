<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserGroupRequest;
use App\Http\Resources\User\ModuleFeatureResource;
use App\Http\Resources\User\RolePermissionResources;
use App\Http\Resources\User\UserGroupResource;
use App\Models\Permission;
use App\Models\Role;
use App\Observers\User\GroupUserObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserGroupController extends Controller
{
    protected $groupUserObserver;

    public function __construct(GroupUserObserver $groupUserObserver)
    {
        $this->groupUserObserver        = $groupUserObserver;
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->groupUserObserver->getData($request);

        $totalRows  = $data->count();
        $roles      = $data->paginate($limit);

        return response()->json([
            'totalRows' => $totalRows,
            'roles'     => UserGroupResource::collection($roles),
        ], 200);
    }

    public function store(UserGroupRequest $request)
    {
        try {

            $this->groupUserObserver->createData($request);

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function update(Request $request, Role $role)
    {
        try {

            $this->groupUserObserver->updateData($request, $role);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function delete(Role $role)
    {
        try {

            $this->groupUserObserver->deleteData($role);

            return response()->json([
                'message'   => 'Data berhasil di hapus',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function modules(Request $request)
    { 
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->groupUserObserver->getModules($request);

        $totalRows  = $data->count();
        $modules    = $data->paginate($limit);

        return response()->json([
            'totalRows' => $totalRows,
            'modules'   => ModuleFeatureResource::collection($modules),
        ], 200);
    }

    public function permissions(Request $request, Role $role)
    {
        $permissions    = $this->groupUserObserver->getPermission($request)->get();

        return response()->json([
            'permissions'   => RolePermissionResources::collection($permissions, ['role' => $role]),
        ], 200);
    }

    public function changePermission(Role $role, Permission $permission)
    {
        try {

            $roleHas = DB::table("role_has_permissions")->where("role_id", $role->id)
                ->where("permission_id", $permission->id)
                ->first();

            if ($roleHas == null) {
                $role->give_permission_data($permission->id);
            }

            if ($roleHas != null) {
                DB::table('role_has_permissions')->where("permission_id", $permission['permission_id'])->where("role_id", $role->id)->delete();
            }

            return response()->json([
                'message'   => 'Berhasil memperbaharui data',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }
}
