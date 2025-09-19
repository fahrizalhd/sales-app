<?php

namespace App\Models;

use App\Traits\Blameable;
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
    use Blameable;
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'item_id',
        'old_quantity',
        'new_quantity',
        'reason',
    ];

    /**
     * Get the item associated with this stock history.
     */    
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * Get the user who created this stock history record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this stock history record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
