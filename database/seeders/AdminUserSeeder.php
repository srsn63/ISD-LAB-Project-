<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database with a default admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mail-admin@gmail.com'],
            [
                'name' => 'Admin',
                'role' => 'admin', // adjust if your schema uses a different field
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
