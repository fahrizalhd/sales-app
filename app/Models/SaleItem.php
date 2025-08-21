<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SaleItem
 *
 * Represents an item included in a sales transaction.
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $sale_id
 * @property int $item_id
 * @property int $quantity
 * @property float $price
 * @property float $subtotal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SaleItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable =
    [
        'sale_id',
        'item_id',
        'quantity',
        'price',
        'subtotal',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the sale that owns this sale item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the item associated with this sale item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
