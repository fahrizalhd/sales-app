<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Beverages (3)
            ['Coca Cola', 'Beverages'],
            ['Aqua Mineral Water', 'Beverages'],
            ['Iced Coffee', 'Beverages'],

            // Snacks (3)
            ['Potato Chips', 'Snacks'],
            ['Chocolate Bar', 'Snacks'],
            ['Seaweed Snack', 'Snacks'],

            // Dairy Products (2)
            ['Fresh Milk 1L', 'Dairy Products'],
            ['Plain Yogurt', 'Dairy Products'],

            // Bakery (2)
            ['White Bread', 'Bakery'],
            ['Chocolate Cake', 'Bakery'],

            // Frozen Foods (2)
            ['Frozen French Fries', 'Frozen Foods'],
            ['Chicken Nuggets', 'Frozen Foods'],

            // Meat & Poultry (3)
            ['Chicken Breast', 'Meat & Poultry'],
            ['Beef Steak', 'Meat & Poultry'],
            ['Smoked Sausage', 'Meat & Poultry'],

            // Seafood (1)
            ['Fresh Salmon', 'Seafood'],

            // Fruits (3)
            ['Apple', 'Fruits'],
            ['Mango', 'Fruits'],
            ['Grapes', 'Fruits'],

            // Vegetables (2)
            ['Carrot', 'Vegetables'],
            ['Spinach', 'Vegetables'],

            // Grains & Cereals (2)
            ['Rice Bag 5kg', 'Grains & Cereals'],
            ['Instant Noodles', 'Grains & Cereals'],

            // Condiments & Sauces (2)
            ['Soy Sauce', 'Condiments & Sauces'],
            ['Chili Sauce', 'Condiments & Sauces'],

            // Personal Care (2)
            ['Shampoo Bottle', 'Personal Care'],
            ['Toothpaste', 'Personal Care'],

            // Household Supplies (3)
            ['Laundry Detergent', 'Household Supplies'],
            ['Dish Soap', 'Household Supplies'],
            ['Floor Cleaner', 'Household Supplies'],

            // Electronics (3)
            ['Wireless Mouse', 'Electronics'],
            ['USB Flash Drive 32GB', 'Electronics'],
            ['Bluetooth Speaker', 'Electronics'],

            // Clothing (2)
            ['T-Shirt', 'Clothing'],
            ['Jeans Pants', 'Clothing'],

            // Footwear (1)
            ['Sneakers', 'Footwear'],

            // Stationery (3)
            ['Ballpoint Pen', 'Stationery'],
            ['Notebook A5', 'Stationery'],
            ['Highlighter', 'Stationery'],

            // Toys & Games (2)
            ['Building Blocks', 'Toys & Games'],
            ['Chess Set', 'Toys & Games'],

            // Sports Equipment (1)
            ['Football Ball', 'Sports Equipment'],

            // Automotive (1)
            ['Engine Oil 1L', 'Automotive'],
        ];

        foreach ($items as [$name, $categoryName]) {
            $category = Category::where('name', $categoryName)->first();

            if ($category) {
                $price = $this->randomPriceByCategory($categoryName);

                Item::create([
                    'name'        => $name,
                    'price'       => $price,
                    'quantity'    => rand(1, 25),
                    'category_id' => $category->id,
                    'sku'         => Item::generateSku(),
                    'is_active'   => (bool)rand(0, 1),
                    'created_by'  => 1,
                    'updated_by'  => 1,
                ]);
            }
        }
    }

    private function randomPriceByCategory(string $category): int
    {
        $raw = match ($category) {
            'Beverages'          => rand(5000, 20000),
            'Snacks'             => rand(3000, 15000),
            'Dairy Products'     => rand(10000, 50000),
            'Bakery'             => rand(8000, 30000),
            'Frozen Foods'       => rand(10000, 60000),
            'Meat & Poultry'     => rand(30000, 150000),
            'Seafood'            => rand(40000, 200000),
            'Fruits'             => rand(5000, 50000),
            'Vegetables'         => rand(3000, 30000),
            'Grains & Cereals'   => rand(10000, 80000),
            'Condiments & Sauces'=> rand(5000, 30000),
            'Personal Care'      => rand(5000, 50000),
            'Household Supplies' => rand(5000, 40000),
            'Electronics'        => rand(50000, 500000),
            'Clothing'           => rand(30000, 300000),
            'Footwear'           => rand(50000, 250000),
            'Stationery'         => rand(2000, 10000),
            'Toys & Games'       => rand(10000, 150000),
            'Sports Equipment'   => rand(20000, 300000),
            'Automotive'         => rand(20000, 500000),
            default              => rand(1000, 10000),
        };

        return round($raw / 1000) * 1000;
    }
}
