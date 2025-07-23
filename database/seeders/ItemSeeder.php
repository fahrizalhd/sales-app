<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $items = [
            ['name' => 'Sabun Mandi', 'price' => 12000, 'qty' => 50],
            ['name' => 'Shampoo Anti Ketombe', 'price' => 18000, 'qty' => 30],
            ['name' => 'Pasta Gigi Herbal', 'price' => 10000, 'qty' => 40],
            ['name' => 'Tisu Basah', 'price' => 15000, 'qty' => 25],
            ['name' => 'Minyak Kayu Putih', 'price' => 17000, 'qty' => 20],
            ['name' => 'Hand Sanitizer', 'price' => 14000, 'qty' => 35],
            ['name' => 'Sikat Gigi', 'price' => 8000, 'qty' => 60],
        ];

        foreach ($items as $item) {
            Item::create([
                'code' => 'SKU-' . mt_rand(1000, 9999),
                'name' => $item['name'],
                'price' => $item['price'],
                'qty' => $item['qty'],
                'image' => null,
            ]);
        }
    }
}
