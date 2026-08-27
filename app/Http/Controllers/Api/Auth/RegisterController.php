<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Http\Resources\Authentication\UserResources;
use App\Mail\User\RegisterNotification;
use App\Models\User;
use App\Observers\Master\UserObserver;
use App\Observers\Notification\NotificationObserver;
use App\Process\MasterData\UploadImageProcess;
use Poshub\Ecommerce\Notifications\EmailVerifyNotify;

class RegisterController extends Controller
{
    protected $userObserver;
    protected $uploadImageProcess;
    protected $notificationObserver;

    public function __construct(UserObserver $userObserver, UploadImageProcess $uploadImageProcess, NotificationObserver $notificationObserver)
    {
        $this->userObserver         = $userObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
        $this->notificationObserver = $notificationObserver;
    }


    public function register(RegisterRequest $request)
    {

        if ($request->password != $request->password_confirmation) {
            return response()->json([
                'status' => false,
                'message' => "Password dan Konfirmasi Password harus sama",
            ], 422);
        }

        $image      = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/users/');
        $user       = $this->userObserver->userRegister($request, $image);

        $templates  = $this->notificationObserver->getTemplate('registration_template');

        if ($templates) {
            $message = str_replace(
                ['{name}', '{email}', '{phone}', '{date}'],
                [$user->name, $user->email, $user->phone, now()->format('Y-m-d')],
                $templates->message
            );

            $this->notificationObserver->sendMessage($message);
        }

        $user->generateTwoFactorCode();
        $user->notify(new EmailVerifyNotify());

        return $this->signin($user);
    }

    public function signin(User $user)
    {
        $token = $user->createToken('MDHDIGITAL346')->plainTextToken;

        return response([
            'status'        => true,
            'message'       => 'Registrasi berhasil di lakukan, silahkan check email anda untuk melakukan konfirmasi email',
            'token'         => $token,
            'data'          => UserResources::make($user)
        ], 200);
    }
}
