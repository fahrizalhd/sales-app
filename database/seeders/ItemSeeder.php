<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Item::create([
                'name' => 'Item ' . $i,
                'description' => 'Description for Item ' . $i,
                'price' => rand(1000, 100000),
                'quantity' => rand(0, 100),
                'sku' => strtoupper(Str::random(8)),
                'image_path' => null,
                'is_active' => rand(0, 1),
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
