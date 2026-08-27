<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class UserManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Role Manager
            [
                'name'          => 'role_view',
                'desc'          => 'Daftar Role',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'add_role',
                'desc'          => 'Tambah Role',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'update_role',
                'desc'          => 'Update Role',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'delete_role',
                'desc'          => 'Hapus Role',
                'guard_name'    => 'web'
            ],


            // User Manager
            [
                'name'          => 'user_view',
                'desc'          => 'Daftar Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'add_user',
                'desc'          => 'Tambah Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'update_user',
                'desc'          => 'Edit Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'delete_user',
                'desc'          => 'Hapus Pengguna',
                'guard_name'    => 'web'
            ],
        ];

        Permission::insert($data);
    }
}
