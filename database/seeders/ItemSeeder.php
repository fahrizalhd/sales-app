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
            ['name' => 'Mineral Water 600ml', 'category' => 'Beverages', 'price' => 5000, 'quantity' => 8],
            ['name' => 'Cola Can 330ml', 'category' => 'Beverages', 'price' => 8000, 'quantity' => 25],
            ['name' => 'Orange Juice 1L', 'category' => 'Beverages', 'price' => 15000, 'quantity' => 12],
            ['name' => 'Iced Coffee Bottle', 'category' => 'Beverages', 'price' => 18000, 'quantity' => 6],
            ['name' => 'Potato Chips', 'category' => 'Snacks', 'price' => 12000, 'quantity' => 30],
            ['name' => 'Chocolate Biscuits', 'category' => 'Snacks', 'price' => 15000, 'quantity' => 25],
            ['name' => 'Peanut Butter Cookies', 'category' => 'Snacks', 'price' => 20000, 'quantity' => 10],
            ['name' => 'Seaweed Snack', 'category' => 'Snacks', 'price' => 7000, 'quantity' => 4],
            ['name' => 'Mixed Nuts 200g', 'category' => 'Snacks', 'price' => 25000, 'quantity' => 14],
            ['name' => 'Full Cream Milk 1L', 'category' => 'Dairy Products', 'price' => 18000, 'quantity' => 20],
            ['name' => 'Cheddar Cheese 250g', 'category' => 'Dairy Products', 'price' => 35000, 'quantity' => 6],
            ['name' => 'Butter 200g', 'category' => 'Dairy Products', 'price' => 28000, 'quantity' => 9],
            ['name' => 'Plain Yogurt 500ml', 'category' => 'Dairy Products', 'price' => 20000, 'quantity' => 15],
            ['name' => 'Whipping Cream 250ml', 'category' => 'Dairy Products', 'price' => 30000, 'quantity' => 7],
            ['name' => 'Whole Wheat Bread', 'category' => 'Bakery', 'price' => 20000, 'quantity' => 10],
            ['name' => 'Chocolate Cake Slice', 'category' => 'Bakery', 'price' => 25000, 'quantity' => 5],
            ['name' => 'Croissant', 'category' => 'Bakery', 'price' => 15000, 'quantity' => 18],
            ['name' => 'Donut Assorted Pack', 'category' => 'Bakery', 'price' => 22000, 'quantity' => 8],
            ['name' => 'Garlic Bread', 'category' => 'Bakery', 'price' => 18000, 'quantity' => 14],
            ['name' => 'Frozen French Fries 1kg', 'category' => 'Frozen Foods', 'price' => 32000, 'quantity' => 12],
            ['name' => 'Frozen Pizza', 'category' => 'Frozen Foods', 'price' => 45000, 'quantity' => 16],
            ['name' => 'Ice Cream Tub 1L', 'category' => 'Frozen Foods', 'price' => 60000, 'quantity' => 20],
            ['name' => 'Frozen Vegetables Mix', 'category' => 'Frozen Foods', 'price' => 30000, 'quantity' => 9],
            ['name' => 'Frozen Nuggets 500g', 'category' => 'Frozen Foods', 'price' => 28000, 'quantity' => 7],
            ['name' => 'Chicken Breast 1kg', 'category' => 'Meat & Poultry', 'price' => 45000, 'quantity' => 18],
            ['name' => 'Ground Beef 500g', 'category' => 'Meat & Poultry', 'price' => 55000, 'quantity' => 12],
            ['name' => 'Lamb Chops 1kg', 'category' => 'Meat & Poultry', 'price' => 120000, 'quantity' => 6],
            ['name' => 'Smoked Sausage 250g', 'category' => 'Meat & Poultry', 'price' => 40000, 'quantity' => 14],
            ['name' => 'Turkey Slices 200g', 'category' => 'Meat & Poultry', 'price' => 35000, 'quantity' => 8],
            ['name' => 'Frozen Shrimp 500g', 'category' => 'Seafood', 'price' => 60000, 'quantity' => 7],
            ['name' => 'Salmon Fillet 200g', 'category' => 'Seafood', 'price' => 80000, 'quantity' => 15],
            ['name' => 'Canned Tuna 185g', 'category' => 'Seafood', 'price' => 18000, 'quantity' => 25],
            ['name' => 'Crab Sticks 250g', 'category' => 'Seafood', 'price' => 30000, 'quantity' => 9],
            ['name' => 'Fresh Tilapia 1kg', 'category' => 'Seafood', 'price' => 50000, 'quantity' => 11],
            ['name' => 'Fresh Apple 1kg', 'category' => 'Fruits', 'price' => 30000, 'quantity' => 15],
            ['name' => 'Fresh Banana 1kg', 'category' => 'Fruits', 'price' => 20000, 'quantity' => 18],
            ['name' => 'Fresh Mango 1kg', 'category' => 'Fruits', 'price' => 35000, 'quantity' => 7],
            ['name' => 'Fresh Orange 1kg', 'category' => 'Fruits', 'price' => 25000, 'quantity' => 20],
            ['name' => 'Fresh Grapes 500g', 'category' => 'Fruits', 'price' => 40000, 'quantity' => 9],
            ['name' => 'Fresh Broccoli 500g', 'category' => 'Vegetables', 'price' => 20000, 'quantity' => 22],
            ['name' => 'Fresh Carrot 1kg', 'category' => 'Vegetables', 'price' => 15000, 'quantity' => 17],
            ['name' => 'Fresh Spinach 250g', 'category' => 'Vegetables', 'price' => 12000, 'quantity' => 6],
            ['name' => 'Fresh Tomato 1kg', 'category' => 'Vegetables', 'price' => 18000, 'quantity' => 21],
            ['name' => 'Fresh Cucumber 1kg', 'category' => 'Vegetables', 'price' => 14000, 'quantity' => 9],
            ['name' => 'White Rice 5kg', 'category' => 'Grains & Cereals', 'price' => 65000, 'quantity' => 28],
            ['name' => 'Brown Rice 2kg', 'category' => 'Grains & Cereals', 'price' => 50000, 'quantity' => 12],
            ['name' => 'Instant Noodles Pack', 'category' => 'Grains & Cereals', 'price' => 3500, 'quantity' => 50],
            ['name' => 'Corn Flakes 500g', 'category' => 'Grains & Cereals', 'price' => 40000, 'quantity' => 7],
            ['name' => 'Oatmeal 1kg', 'category' => 'Grains & Cereals', 'price' => 60000, 'quantity' => 15],
            ['name' => 'Tomato Ketchup 340ml', 'category' => 'Condiments & Sauces', 'price' => 15000, 'quantity' => 9],
            ['name' => 'Soy Sauce 150ml', 'category' => 'Condiments & Sauces', 'price' => 12000, 'quantity' => 25],
            ['name' => 'Chili Sauce 200ml', 'category' => 'Condiments & Sauces', 'price' => 15000, 'quantity' => 10],
            ['name' => 'Mayonnaise 250ml', 'category' => 'Condiments & Sauces', 'price' => 22000, 'quantity' => 8],
            ['name' => 'Black Pepper Powder 50g', 'category' => 'Condiments & Sauces', 'price' => 18000, 'quantity' => 12],
            ['name' => 'Herbal Shampoo 200ml', 'category' => 'Personal Care', 'price' => 25000, 'quantity' => 13],
            ['name' => 'Body Soap Bar', 'category' => 'Personal Care', 'price' => 7000, 'quantity' => 40],
            ['name' => 'Toothpaste 150g', 'category' => 'Personal Care', 'price' => 18000, 'quantity' => 9],
            ['name' => 'Face Wash 100ml', 'category' => 'Personal Care', 'price' => 30000, 'quantity' => 14],
            ['name' => 'Moisturizer Cream 50g', 'category' => 'Personal Care', 'price' => 45000, 'quantity' => 6],
            ['name' => 'Laundry Detergent 1kg', 'category' => 'Household Supplies', 'price' => 28000, 'quantity' => 21],
            ['name' => 'Floor Cleaner 500ml', 'category' => 'Household Supplies', 'price' => 20000, 'quantity' => 11],
            ['name' => 'Kitchen Tissue Roll', 'category' => 'Household Supplies', 'price' => 15000, 'quantity' => 18],
            ['name' => 'Toilet Cleaner 500ml', 'category' => 'Household Supplies', 'price' => 18000, 'quantity' => 7],
            ['name' => 'Dishwashing Liquid 500ml', 'category' => 'Household Supplies', 'price' => 16000, 'quantity' => 13],
            ['name' => 'Wireless Mouse', 'category' => 'Electronics', 'price' => 75000, 'quantity' => 14],
            ['name' => 'USB Flash Drive 32GB', 'category' => 'Electronics', 'price' => 120000, 'quantity' => 20],
            ['name' => 'Bluetooth Speaker', 'category' => 'Electronics', 'price' => 300000, 'quantity' => 9],
            ['name' => 'LED Light Bulb', 'category' => 'Electronics', 'price' => 25000, 'quantity' => 35],
            ['name' => 'Power Bank 10000mAh', 'category' => 'Electronics', 'price' => 250000, 'quantity' => 7],
            ['name' => 'Cotton T-Shirt', 'category' => 'Clothing', 'price' => 50000, 'quantity' => 4],
            ['name' => 'Jeans Pants', 'category' => 'Clothing', 'price' => 150000, 'quantity' => 18],
            ['name' => 'Jacket Hoodie', 'category' => 'Clothing', 'price' => 200000, 'quantity' => 9],
            ['name' => 'Formal Shirt', 'category' => 'Clothing', 'price' => 175000, 'quantity' => 15],
            ['name' => 'Summer Dress', 'category' => 'Clothing', 'price' => 220000, 'quantity' => 12],
            ['name' => 'Casual Sneakers', 'category' => 'Footwear', 'price' => 120000, 'quantity' => 16],
            ['name' => 'Leather Shoes', 'category' => 'Footwear', 'price' => 250000, 'quantity' => 8],
            ['name' => 'Flip Flops', 'category' => 'Footwear', 'price' => 35000, 'quantity' => 25],
            ['name' => 'Running Shoes', 'category' => 'Footwear', 'price' => 180000, 'quantity' => 14],
            ['name' => 'High Heels', 'category' => 'Footwear', 'price' => 200000, 'quantity' => 6],
            ['name' => 'Ballpoint Pen Pack', 'category' => 'Stationery', 'price' => 20000, 'quantity' => 30],
            ['name' => 'Notebook A5', 'category' => 'Stationery', 'price' => 15000, 'quantity' => 18],
            ['name' => 'Sticky Notes', 'category' => 'Stationery', 'price' => 12000, 'quantity' => 40],
            ['name' => 'Highlighter Set', 'category' => 'Stationery', 'price' => 35000, 'quantity' => 7],
            ['name' => 'Binder Clips Set', 'category' => 'Stationery', 'price' => 10000, 'quantity' => 20],
            ['name' => 'Building Blocks Set', 'category' => 'Toys & Games', 'price' => 150000, 'quantity' => 10],
            ['name' => 'Board Game Classic', 'category' => 'Toys & Games', 'price' => 250000, 'quantity' => 7],
            ['name' => 'Puzzle 1000 pieces', 'category' => 'Toys & Games', 'price' => 120000, 'quantity' => 14],
            ['name' => 'RC Car', 'category' => 'Toys & Games', 'price' => 350000, 'quantity' => 6],
            ['name' => 'Doll Set', 'category' => 'Toys & Games', 'price' => 180000, 'quantity' => 12],
            ['name' => 'Yoga Mat', 'category' => 'Sports Equipment', 'price' => 120000, 'quantity' => 15],
            ['name' => 'Dumbbell Set', 'category' => 'Sports Equipment', 'price' => 250000, 'quantity' => 9],
            ['name' => 'Football Ball', 'category' => 'Sports Equipment', 'price' => 180000, 'quantity' => 13],
            ['name' => 'Tennis Racket', 'category' => 'Sports Equipment', 'price' => 300000, 'quantity' => 7],
            ['name' => 'Skipping Rope', 'category' => 'Sports Equipment', 'price' => 50000, 'quantity' => 20],
            ['name' => 'Motor Oil 1L', 'category' => 'Automotive', 'price' => 75000, 'quantity' => 15],
            ['name' => 'Car Shampoo 1L', 'category' => 'Automotive', 'price' => 50000, 'quantity' => 12],
            ['name' => 'Air Freshener', 'category' => 'Automotive', 'price' => 30000, 'quantity' => 40],
            ['name' => 'Car Battery 45Ah', 'category' => 'Automotive', 'price' => 850000, 'quantity' => 6],
            ['name' => 'Tire Polish 500ml', 'category' => 'Automotive', 'price' => 40000, 'quantity' => 9],
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
