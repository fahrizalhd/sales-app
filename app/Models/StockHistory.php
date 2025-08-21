<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StockHistory
 *
 * Represents a record of stock changes for an item.
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $item_id
 * @property int $change
 * @property int $old_quantity
 * @property int $new_quantity
 * @property string|null $reason
 * @property int $created_by
 * @property int $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class StockHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'change',
        'old_quantity',
        'new_quantity',
        'reason',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the item associated with this stock history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the user who created this stock history record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this stock history record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
