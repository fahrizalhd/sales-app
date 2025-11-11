<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Call the UserSeeder to seed users
        $this->call(UserSeeder::class);

        // Call the CategorySeeder to seed categories
        $this->call(CategorySeeder::class);

        // Call the ItemSeeder to seed items
        $this->call(ItemSeeder::class);

        // Call the SaleFactory to seed sales
        $randomUser = User::where('role', UserRole::USER->value)->inRandomOrder()->first();
        Auth::login($randomUser);
        // Sale::factory()->count(80)->create();
        Auth::logout();
    }
}
