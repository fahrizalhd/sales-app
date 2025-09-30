<?php

namespace Database\Factories;

use App\Enums\SaleStatus;
use App\Models\Item;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    /**
     * Define the model's default state.
     *
     */
    protected static array $counters = [];
    protected static array $customers = [
        'Haris Pratama',
        'Alya Putri',
        'Budi Santoso',
        'Citra Dewi',
        'Deni Nugroho',
        'Eka Lestari',
        'Fajar Ramadhan',
        'Gina Maharani',
        'Hadi Setiawan',
        'Intan Permata',
    ];

    private function getRandomCustomer(): string
    {
        if (mt_rand(1, 100) <= 60) {
            return self::$customers[array_rand(self::$customers)];
        }
        return $this->faker->name();
    }

    public function definition(): array
    {
        $transactionDate = $this->faker->dateTimeBetween('-2 months', 'now');
        $dateForInvoice = $transactionDate->format('Ymd');

        if (!isset(self::$counters[$dateForInvoice])) {
            self::$counters[$dateForInvoice] = 1;
        } else {
            self::$counters[$dateForInvoice]++;
        }

        $invoiceNumber = 'INV-' . $dateForInvoice . '-' . str_pad(self::$counters[$dateForInvoice], 4, '0', STR_PAD_LEFT);

        return [
            'invoice_number'    => $invoiceNumber,
            'customer_name'     => $this->getRandomCustomer(),
            'total_amount'      => 0,
            'status'            => SaleStatus::UNPAID->value,
            'transaction_date'  => $transactionDate,
            'created_at'        => $transactionDate,
            'updated_at'        => $transactionDate,
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
