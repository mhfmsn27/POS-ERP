<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Observers\Master\UserObserver;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected $usersObserver;

    public function __construct(UserObserver $usersObserver)
    {
        $this->usersObserver        = $usersObserver;
    }

    public function index(Request $request)
    {
        $users  = $this->usersObserver->getData($request, 'administrator')->get(['id', 'name', 'photo', 'email', 'phone', 'jk']);
        return view('super.user.index', ['page' => 'Daftar Pengguna'], compact('users'));
    }

    public function create()
    {
        return view('super.user.create', ['page' => 'Tambah Pengguna']);
    }

    public function update(User $user)
    {
        return view('super.user.update', ['page' => 'Edit Pengguna'], compact('user'));
    }

    public function delete(User $user)
    {
        $user->delete();
        return redirect()->route('administrator.user')->with(['flash' => 'Data Pengguna berhasil di hapus']);
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|numeric',
            'jk'            => 'required|in:pria,wanita',
            'photo'         => 'mimes:png,jpg,jpeg',
            'password'      => 'required|min:8'
        ]);

        try {

            $image      = $request->photo ? $this->uploadImage($request, 'photo', 'users') : '';

            $this->usersObserver->createDataAdministrator($request, $image);

            return redirect()->route('administrator.user')->with(['flash' => 'Data Pengguna berhasil di tambahkan']);
        } catch (\Exception $e) {

            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }

    public function edit(Request $request, User $user)
    {
        $this->validate($request, [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone'         => 'required|numeric',
            'jk'            => 'required|in:pria,wanita',
            'photo'         => 'mimes:png,jpg,jpeg'
        ]);

        try {

            $image      = $request->photo ? $this->uploadImage($request, 'photo', 'users') : '';

            $this->usersObserver->updateDataAdministrator($request, $user, $image);

            return redirect()->route('administrator.user')->with(['flash' => 'Data Pengguna berhasil di perbaharui']);
        } catch (\Exception $e) {

            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }
}
