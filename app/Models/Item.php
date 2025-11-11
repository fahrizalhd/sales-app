<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class Item
 *
 * Represents an item/product in the inventory.
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property int $quantity
 * @property string $sku
 * @property string|null $image_path
 * @property bool $is_active
 * @property int $category_id
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Item extends Model
{
    use SoftDeletes, Blameable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'cost',
        'quantity',
        'sku',
        'image_path',
        'is_active',
        'category_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'price'     => 'decimal:2',
        'cost'      => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created the item.
     */    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the item.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the stock histories for the item.
     */
    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'item_id');
    }

    /**
     * Get the category that the item belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withDefault([
            'name' => 'Uncategorized',
        ]);
    }

    /**
     * Generate a unique SKU for the item.
     */
    public static function generateSku()
    {
        // Define a prefix for the SKU
        $prefix = 'SKU';

        // Generate a unique SKU, you can customize this logic as needed
        $randomString = strtoupper(Str::random(4));

        // Ensure the SKU is unique
        while (self::where('sku', $prefix . $randomString)->exists()) {
            $randomString = strtoupper(Str::random(8));
        }

        // Return the SKU with the prefix
        return $prefix . "-" . $randomString;
    }

    /**
     * Cascade soft delete related items.
     */
    public static function booted()
    {
        static::deleting(function ($sale) {
            $sale->stockHistories->each->delete();
        });
    }
}
