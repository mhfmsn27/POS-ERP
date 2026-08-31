<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userData = [
            'name'              => 'Administrator POSHUB', 
            'email'             => 'admin@poshub.id',
            'password'          => Hash::make('password123'),
            'photo'             => 'uploads/image.jpg',
            'email_verified_at' => now(),
            'role_type'         => 'administrator'
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'store_id')) {
            $userData['store_id'] = 1;
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'merchant_id')) {
            $userData['merchant_id'] = 1;
        }

        User::updateOrCreate(
            ['email' => 'admin@poshub.id'],
            $userData
        ); 
    }
}
