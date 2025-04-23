<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'ayatarefin@ordercraft.com'],
            [
                'name' => 'Ayatullah Arefin',
                'password' => Hash::make('password123456'),
                'role' => 'admin',
            ]
        );
    }
}
