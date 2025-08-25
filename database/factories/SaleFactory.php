<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected static array $counters = [];

    public function definition(): array
    {
        $dateTime = $this->faker->dateTimeBetween('-2 months', 'now');
        $dateForInvoice = $dateTime->format('Ymd');

        if (!isset(self::$counters[$dateForInvoice])) {
            self::$counters[$dateForInvoice] = 1;
        } else {
            self::$counters[$dateForInvoice]++;
        }

        $invoiceNumber = 'INV-' . $dateForInvoice . '-' . str_pad(self::$counters[$dateForInvoice], 4, '0', STR_PAD_LEFT);

        return [
            'invoice_number' => $invoiceNumber,
            'customer_name' => $this->faker->name(),
            'total_amount' => 0,
            'is_paid' => false,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
    }


    public function configure()
    {
        return $this->afterCreating(function (Sale $sale) {
            $total = 0;

            $items = Item::where('is_active', true)->inRandomOrder()->take(rand(1, 5))->get();

            foreach ($items as $item) {
                $qty = rand(1, 3);
                $price = $item->price;
                $subtotal = $qty * $price;

                $sale->saleItems()->create([
                    'sale_id' => $sale->id,
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $sale->update(['total_amount' => $total]);
        });
    }
}
