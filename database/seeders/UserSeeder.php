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
        User::firstOrCreate(
            ['email' => 'admin@hrmanager.local'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@hrmanager.local'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]
        );
    }
}
