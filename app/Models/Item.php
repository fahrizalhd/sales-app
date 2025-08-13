<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Item extends Model
{
    use SoftDeletes, Blameable;

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'sku',
        'image_path',
        'is_active',
        'category_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class, 'item_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Generate a unique SKU for the item.
     *
     * @return string
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
}
