<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default users with roles
        User::updateOrCreate(
            ['email' => 'dev@sales.app'],
            [
                'name' => 'Developer',
                'email' => 'dev@sales.app',
                'password' => Hash::make('dev'),
                'role' => UserRole::SUPERADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@sales.app'],
            [
                'name' => 'Admin',
                'email' => 'admin@sales.app',
                'password' => Hash::make('admin'),
                'role' => UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@sales.app'],
            [
                'name' => 'Cashier',
                'email' => 'user@sales.app',
                'password' => Hash::make('user'),
                'role' => UserRole::USER,
                'email_verified_at' => now(),
            ]
        );
    }
}
