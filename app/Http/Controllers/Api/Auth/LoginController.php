<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Resources\Authentication\UserResources;
use App\Models\User;
use App\Observers\Master\UserObserver; 
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected $usersObserver;

    public function __construct(UserObserver $userObserver)
    {
        $this->usersObserver        = $userObserver;
    }

    public function login(LoginRequest $request)
    {

        // Checking User By Email and Role
        $user = User::where('email', $request->email)->first();

        // Response If User Not Available
        if ($user == null) {
            return response([
                'status'    => false,
                'message' => 'Maaf, Email Anda belum terdaftar di sistem kami'
            ], 422);
        }

        // Check User Password
        $checkPass = User::where("password", "!=", Hash::check($request->password, $user->password))->first();

        // Response If Password is Wrong
        if ($checkPass == null) {
            return response([
                'status'    => false,
                'message' => 'Sepertinya Password yang anda masukkan salah'
            ], 422);
        }

        // Response if User Email and Password is Wrong
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status'    => false,
                'message' => 'Kombinasi Antara Email / dengan Password salah'
            ], 422);
        }

        // Generate Token Access API
        $token      = $user->createToken('MDHDIGITAL346')->plainTextToken;
         

        // Response if Success Login
        return response([
            'status'    => true,
            'token'     => $token, 
            'data'      => UserResources::make($user), 
        ], 200);
    }

    public function logout()
    {
        // Logout User and Delete Access API Token
        if (auth()->check()) {
            auth()->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => true,
                'message' => "Berhasil Sign Out",
            ]);
        }
    }
}
