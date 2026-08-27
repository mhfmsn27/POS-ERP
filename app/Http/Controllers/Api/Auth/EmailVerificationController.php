<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\TwoFactorCodeRequest;
use App\Notifications\User\EmailVerificationNotification;
use Poshub\Ecommerce\Notifications\EmailVerifyNotify;

class EmailVerificationController extends Controller
{
    public function resend()
    {

        $user = auth()->user();

        if ($user->email_verified_at != null) {
            return response()->json([
                'status' => false,
                'message' => 'Email anda sudah ter-verifikasi sebelumnya',
            ], 422);
        }

        $user->generateTwoFactorCode();
        $user->notify(new EmailVerificationNotification()); 

        return response()->json([
            'status'    => true,
            'message'   => 'Kode Verifikasi Email sudah di kirimkan kembali.',
        ], 200);
    }

    public function store(TwoFactorCodeRequest $request)
    { 
        $user = auth()->user();

        if ($user->two_factor_code) {
            if ($user->two_factor_expires_at->lt(now())) {

                $user->resetTwoFactorCode();

                return response()->json([
                    'status' => false,
                    'message' => 'Kode dua faktor telah kedaluwarsa. Silakan Coba kembali.',
                ], 419);
            }
        }

        if ($user->email_verified_at != null) {
            return response()->json([
                'status' => false,
                'message' => 'Anda Sudah melakukan verifikasi sebelumnya',
            ], 419);
        }

        if ($request->input('two_factor_code') == $user->two_factor_code) {

            $user->resetTwoFactorCode();
            $user->email_verified_at = now();
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Email berhasil di verifikasi',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Kode dua faktor yang Anda masukkan tidak cocok',
        ], 419);
    }
}
