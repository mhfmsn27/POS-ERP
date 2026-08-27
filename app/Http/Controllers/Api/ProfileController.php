<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\ChangePasswordRequest;
use App\Http\Requests\Authentication\ChangeProfileRequest;
use App\Http\Resources\Authentication\UserResources;
use App\Models\User;
use App\Observers\Master\UserObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected $userObserver;
    protected $uploadImageProcess;

    public function __construct(UserObserver $userObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->userObserver         = $userObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
    }

    public function index()
    {
        return response()->json(UserResources::make(auth()->user()));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Change Profile
    |--------------------------------------------------------------------------
    */

    public function changeProfile(ChangeProfileRequest $request)
    {
        try {

            $user   = User::find(auth()->user()->id);
            $image  = '';


            if ($request->image) {
                $icon = explode(',', $request->image);
                if (count($icon) == 2 && substr($icon[0], 0, 5) === 'data:') {
                    $imageData = base64_decode($icon[1]);

                    if ($imageData !== false && getimagesizefromstring($imageData) !== false) {
                        $this->uploadImageProcess->unlinkFile(auth()->user()->photo);
                        $image = $this->uploadImageProcess->uploadFile($request->image, $request->name, 'uploads/users/');
                    }
                }
            } else {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/users/');
            }


            $this->userObserver->updateDataAdministrator($request, $user, $image);

            return response()->json([
                'message'   => 'Berhasil memperbaharui profile',
                'detail'    => UserResources::make(auth()->user()),
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Change Password
    |--------------------------------------------------------------------------
    */

    public function changePassword(ChangePasswordRequest $request)
    {
        try {

            if ($request->confirm != $request->new) {
                return response([
                    'status'    => false,
                    'message'   => 'Password dan konfirmasi password harus sama'
                ], 422);
            }


            $user       = User::find(auth()->user()->id);
            $checkPass  = User::where("password", "!=", Hash::check($request->old, $user->password))->first();

            if ($checkPass == null) {
                return response([
                    'status'    => false,
                    'message'   => 'Password lama anda salah'
                ], 422);
            }

            $user->update([
                'password'              => Hash::make($request->new)
            ]);

            return response()->json([
                'message'   => 'Berhasil memperbaharui password',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }
}
