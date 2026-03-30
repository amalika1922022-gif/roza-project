<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // الإيميل تبع الأدمن
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'), // غيريها لو بدك
                'role' => 'admin',
                'is_blocked' => false,
            ]
        );
    }
}
