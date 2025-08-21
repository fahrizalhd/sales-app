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
            ['name' => 'Mineral Water 600ml', 'category' => 'Beverages', 'price' => 5000, 'quantity' => 8], // <10
            ['name' => 'Potato Chips', 'category' => 'Snacks', 'price' => 12000, 'quantity' => 30],
            ['name' => 'Chocolate Biscuits', 'category' => 'Snacks', 'price' => 15000, 'quantity' => 25],
            ['name' => 'Full Cream Milk 1L', 'category' => 'Dairy Products', 'price' => 18000, 'quantity' => 20],
            ['name' => 'Cheddar Cheese 250g', 'category' => 'Dairy Products', 'price' => 35000, 'quantity' => 6], // <10
            ['name' => 'Whole Wheat Bread', 'category' => 'Bakery', 'price' => 20000, 'quantity' => 10],
            ['name' => 'Chocolate Cake Slice', 'category' => 'Bakery', 'price' => 25000, 'quantity' => 5], // <10
            ['name' => 'Frozen French Fries 1kg', 'category' => 'Frozen Foods', 'price' => 32000, 'quantity' => 12],
            ['name' => 'Chicken Breast 1kg', 'category' => 'Meat & Poultry', 'price' => 45000, 'quantity' => 18],
            ['name' => 'Frozen Shrimp 500g', 'category' => 'Seafood', 'price' => 60000, 'quantity' => 7], // <10
            ['name' => 'Fresh Apple 1kg', 'category' => 'Fruits', 'price' => 30000, 'quantity' => 15],
            ['name' => 'Fresh Broccoli 500g', 'category' => 'Vegetables', 'price' => 20000, 'quantity' => 22],
            ['name' => 'White Rice 5kg', 'category' => 'Grains & Cereals', 'price' => 65000, 'quantity' => 28],
            ['name' => 'Tomato Ketchup 340ml', 'category' => 'Condiments & Sauces', 'price' => 15000, 'quantity' => 9], // <10
            ['name' => 'Herbal Shampoo 200ml', 'category' => 'Personal Care', 'price' => 25000, 'quantity' => 13],
            ['name' => 'Laundry Detergent 1kg', 'category' => 'Household Supplies', 'price' => 28000, 'quantity' => 21],
            ['name' => 'Wireless Mouse', 'category' => 'Electronics', 'price' => 75000, 'quantity' => 14],
            ['name' => 'Cotton T-Shirt', 'category' => 'Clothing', 'price' => 50000, 'quantity' => 4], // <10
            ['name' => 'Casual Sneakers', 'category' => 'Footwear', 'price' => 120000, 'quantity' => 16],
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
