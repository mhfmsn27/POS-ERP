<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\EmailRequest;
use App\Http\Requests\Authentication\ForgetPasswordRequest;
use App\Http\Requests\Authentication\TwoFactorCodeRequest;
use App\Http\Resources\Authentication\UserResources;
use App\Models\User;
use App\Notifications\User\ForgetPasswordNotification;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function sendAsk(EmailRequest $request)
    {
        $this->validate($request, [
            'email'     => 'required|email'
        ]);

        $getUsers = User::where("email", $request->email)->first();

        if ($getUsers == null) {
            return response()->json([
                'status' => false,
                'message' => 'Email atau Akun tidak ditemukan',
            ], 422);
        }

        $getUsers->generateTwoFactorCode();

        $getUsers->notify(new ForgetPasswordNotification());

        return response()->json([
            'status'    => true,
            'message'   => 'Kode Berisikan Verifikasi Email telah di kirimkan kepada anda melalui Email',
        ], 200);
    }

    public function verifyTwoFactor(TwoFactorCodeRequest $request)
    {

        $this->validate($request, [
            'two_factor_code'     => 'required'
        ]);


        $code = $request->two_factor_code;

        $user = User::where("two_factor_code", $code)->first();

        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'Kode Permintaan Tidak dikenali',
            ], 422);
        }

        if ($user->two_factor_code) {
            if ($user->two_factor_expires_at->lt(now())) {

                $user->resetTwoFactorCode();

                return response()->json([
                    'status' => false,
                    'message' => 'Kode dua faktor telah kedaluwarsa. Silakan Minta kembali kode.',
                ], 422);
            }
        }

        if ($code == $user->two_factor_code) {

            return response()->json([
                'status'            => true,
                'message'           => 'Email berhasil di verifikasi, Silahkan lanjutkan dengan mengubah password Anda',
            ], 200);
        }
    }

    public function changePassword(ForgetPasswordRequest $request)
    {  

        if ($request->password != $request->password_confirmation) {
            return response()->json([
                'status' => false,
                'message' => 'Password dan Konfirmasi Password Tidak Sama',
            ], 422);
        }

        $code = $request->verify_email['two_factor_code'];
        $user = User::where("two_factor_code", $code)->where("email", $request->email)->first();


        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan, silahkan refresh dan ulang kembali proses',
            ], 422);
        }

        if ($code == $user->two_factor_code) {
            $user->resetTwoFactorCode();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $token = $user->createToken('MDH007')->plainTextToken;

        return response([
            'status'        => true,
            'token'         => $token, 
            'message'       => 'Kata Sandi berhasil di ubah, Anda akan segera di alihkan ke halaman dashboard',
            'data'          => UserResources::make($user),
        ], 200);
    }
}
