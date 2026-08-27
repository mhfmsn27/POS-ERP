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
        $superAdmin = User::create([
            'name'              => 'Administrator POSHUB', 
            'email'             => 'admin@poshub.id',
            'password'          => Hash::make('password123'),
            'photo'             => 'uploads/image.jpg',
            'email_verified_at' => now(),
            'role_type'         => 'administrator'
        ]); 
    }
}
