<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Sale
 *
 * Represents a sales transaction.
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $invoice_number
 * @property float $total_amount
 * @property bool $is_paid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Sale extends Model
{
    use SoftDeletes, Blameable, HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable =
    [
        'invoice_number',
        'customer_name',
        'total_amount',
        'is_paid',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'is_paid' => 'boolean',
    ];

    /**
     * Get the sale items for this sale.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the user who created the sale.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the sale.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the list of payments associated with the sale.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Generate a unique invoice number for the sale.
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');

        $lastSale = self::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastSale) {
            $lastCounter = (int)substr($lastSale->invoice_number, -4);
            $counter = str_pad($lastCounter + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $counter = '0001';
        }

        return "{$prefix}-{$date}-{$counter}";
    }

    /**
     * Model booted events.
     *
     * - Automatically generate an invoice number when creating a Sale. // removed temporary
     * - Cascade soft delete to related SaleItems when deleting a Sale.
     */
    public static function booted()
    {
        // static::creating(function ($sale) {
        //     $sale->invoice_number->self::generateInvoiceNumber();
        // });

        static::deleting(function ($sale) {
            $sale->saleItems->each->delete();
        });
    }
}
