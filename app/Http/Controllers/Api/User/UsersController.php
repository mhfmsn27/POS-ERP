<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserDetailResource;
use App\Http\Resources\User\UserListResource;
use App\Models\User;
use App\Observers\Master\UserObserver;
use App\Observers\Notification\NotificationObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    protected $usersObserver;
    protected $notificationObserver;

    public function __construct(UserObserver $usersObserver, NotificationObserver $notificationObserver)
    {
        $this->usersObserver        = $usersObserver;
        $this->notificationObserver = $notificationObserver;
    }

    public function index(Request $request)
    {
        $limit  = $request->limit ? $request->limit : 10;
        $data   = $this->usersObserver->getData($request);

        $totalRows  = $data->count();
        $users      = $data->paginate($limit);

        return response()->json([
            'totalRows' => $totalRows,
            'users'     => UserListResource::collection($users),
        ], 200);
    }

    public function detail(User $user)
    {
        return response()->json(UserDetailResource::make($user), 200);
    }

    public function store(UserCreateRequest $request)
    {
        try {

            DB::beginTransaction();

            $user       = $this->usersObserver->userRegister($request, '');

            $user->update([
                'role'          => $request->role,
                'store_id'      => implode(',', array_column($request->stores, 'id')),
            ]);

            $this->usersObserver->giveAccess($user, 'create');

            $templates  = $this->notificationObserver->getTemplate('user_template');

            if ($templates) {
                $message = str_replace(
                    ['{name}', '{email}', '{password}', '{createdby}', '{date}'],
                    [$user->name, $user->email, $request->password, auth()->user()->name, now()->format('Y-m-d')],
                    $templates->message
                );
    
                $this->notificationObserver->sendMessage($message, $user->phone);
            }

            DB::commit();

            

            return response()->json([
                'message'   => 'Data berhasil di tambahkan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 409);
        }
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        try {

            DB::beginTransaction();

            $this->usersObserver->updateData($request, $user);

            DB::commit();

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message'   => $e->getMessage(), 
                'status'    => false
            ], 409);
        }
    }

    public function delete(User $user)
    {
        try {

            $user->delete();

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
}
