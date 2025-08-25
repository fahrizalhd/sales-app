<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleItem>
 */
class SaleItemFactory extends Factory
{
    protected $model = SaleItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $item = Item::where('is_active', true)->inRandomOrder()->first();

        return [
            'sale_id'   => Sale::factory(),
            'item_id'   => $item->id,
            'price'     => $item->price,
            'quantity'  => $this->faker->numberBetween(1, 5),
            'subtotal'  => fn(array $attrs) => $attrs['price'] * $attrs['quantity'],
            'created_by'=> 1,
            'updated_by'=> 1,
        ];
    }
}
