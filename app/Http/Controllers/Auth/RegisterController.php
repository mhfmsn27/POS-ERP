<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Models\User;
use App\Observers\Notification\NotificationObserver;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $notificationObserver;

    public function __construct(NotificationObserver $notificationObserver)
    {
        $this->notificationObserver     = $notificationObserver;
        $this->middleware('guest');
    }

    public function index()
    {
        return view('auth.register', ['page' => 'Daftar Pengguna']);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(RegisterRequest $request)
    {

        if ($request->password != $request->password_confirmation) {
            return redirect()->back()->with(['gagal' => 'Password dan konfirmasi password harus sama']);
        }

        $data = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'jk'        => $request->jk,
            'phone'     => $request->phone,
        ]);

        $templates  = $this->notificationObserver->getTemplate('registration_template');

        if ($templates) {
            $message = str_replace(
                ['{name}', '{email}', '{phone}', '{date}'],
                [$data->name, $data->email, $data->phone, now()->format('Y-m-d')],
                $templates->message
            );

            $this->notificationObserver->sendMessage($message);
        }


        return redirect()->route('login')->with(['flash' => 'Berhasil mendaftarkan akun, silahkan login dan verifikasi alamat email']);
    }
}
