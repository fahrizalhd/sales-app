<?php

namespace Database\Seeders;

use App\Models\Category;
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
            ['name' => 'Green Tea Bottle', 'category' => 'Beverages', 'price' => 10000, 'quantity' => 40],
            ['name' => 'Potato Chips', 'category' => 'Snacks', 'price' => 12000, 'quantity' => 30],
            ['name' => 'Chocolate Biscuits', 'category' => 'Snacks', 'price' => 15000, 'quantity' => 25],
            ['name' => 'Full Cream Milk 1L', 'category' => 'Dairy Products', 'price' => 18000, 'quantity' => 20],
            ['name' => 'Cheddar Cheese 250g', 'category' => 'Dairy Products', 'price' => 35000, 'quantity' => 15],
            ['name' => 'Whole Wheat Bread', 'category' => 'Bakery', 'price' => 20000, 'quantity' => 10],
        ];

        foreach ($items as $item) {
            $category = Category::where('name', $item['category'])->first();

            if ($category) {
                Item::create([
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'category_id' => $category->id,
                    'sku' => Item::generateSku(),
                    'is_active' => rand(true, false),
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
            }
        }
    }
}
