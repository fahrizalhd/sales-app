<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Beverages', 'description' => 'Soft drinks, coffee, tea, and bottled water'],
            ['name' => 'Snacks', 'description' => 'Chips, biscuits, and light snacks'],
            ['name' => 'Dairy Products', 'description' => 'Milk, cheese, butter, and yogurt'],
            ['name' => 'Bakery', 'description' => 'Bread, cakes, and pastries'],
            // ['name' => 'Frozen Foods', 'description' => 'Ice cream, frozen meals, and frozen vegetables'],
            // ['name' => 'Meat & Poultry', 'description' => 'Fresh and processed meat products'],
            // ['name' => 'Seafood', 'description' => 'Fresh and frozen fish, shrimp, and shellfish'],
            // ['name' => 'Fruits', 'description' => 'Fresh and dried fruits'],
            // ['name' => 'Vegetables', 'description' => 'Fresh, canned, and frozen vegetables'],
            // ['name' => 'Grains & Cereals', 'description' => 'Rice, pasta, noodles, and breakfast cereals'],
            // ['name' => 'Condiments & Sauces', 'description' => 'Ketchup, mayonnaise, soy sauce, and spices'],
            // ['name' => 'Personal Care', 'description' => 'Shampoo, soap, toothpaste, and skincare'],
            // ['name' => 'Household Supplies', 'description' => 'Cleaning products, detergents, and tissues'],
            // ['name' => 'Electronics', 'description' => 'Gadgets, accessories, and household electronics'],
            // ['name' => 'Clothing', 'description' => 'Men’s, women’s, and children’s apparel'],
            // ['name' => 'Footwear', 'description' => 'Shoes, sandals, and slippers'],
            // ['name' => 'Stationery', 'description' => 'Office and school supplies'],
            // ['name' => 'Toys & Games', 'description' => 'Children’s toys, board games, and puzzles'],
            // ['name' => 'Sports Equipment', 'description' => 'Fitness gear and sports accessories'],
            // ['name' => 'Automotive', 'description' => 'Car accessories, lubricants, and spare parts'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                    'created_by' => 1,
                    'updated_by' => 1,
                ]
            );
        }
    }
}
