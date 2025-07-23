<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // for ($i = 1; $i <= 5; $i++) {
        //     User::create([
        //         'name' => "user_$i",
        //         'email' => "user_{$i}@sales.app",
        //         'password' => Hash::make('password'),
        //     ]);
        // }
    }
}
