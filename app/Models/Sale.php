<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\SaleStatus;
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
 * @property string $customer_name
 * @property float $total_amount
 * @property SaleStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Sale extends Model
{
    use SoftDeletes, Blameable, HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice_number',
        'customer_name',
        'total_amount',
        'status',
        'transaction_date'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'status'            => SaleStatus::class,
        'transaction_date'  => 'datetime',
    ];

    /**
     * Relationship: Get all sale items linked to this sale.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Relationship: Get all payments linked to this sale.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relationship: Get the user who created this sale.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Get the user who last updated this sale.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Generate a unique invoice number for a new sale.
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
     * Get the list of statuses considered as "paid".
     */
    public static function paidStatuses(): array
    {
        return [
            SaleStatus::PAID->value,
            SaleStatus::NEED_REVIEW->value,
        ];
    }

    /**
     * Get the list of statuses considered as "unpaid".
     */
    public static function unpaidStatuses(): array
    {
        return [
            SaleStatus::UNPAID->value,
            SaleStatus::CANCELLED->value,
        ];
    }

    /**
     * Statuses that can proceed to payment.
     */
    public static function canBePaid(): array
    {
        return [
            SaleStatus::UNPAID->value,
        ];
    }


    /**
     * Get the list of statuses can be deleted.
     */
    public static function canBeEdited(): array
    {
        return [
            SaleStatus::UNPAID->value,
        ];
    }

    /**
     * Get the list of statuses can be deleted.
     */
    public static function canBeDeleted(): array
    {
        return [
            SaleStatus::UNPAID->value,
        ];
    }

    /**
     * Get the list of statuses can be cancelled.
     */
    public static function canBeCancelled(): array
    {
        return [
            SaleStatus::UNPAID->value,
        ];
    }

    /**
     * Determine if the sale has any refunded payments.
     */
    public function getIsRefundedAttribute(): bool
    {
        return $this->payments()->where('status', PaymentStatus::REFUNDED)->exists();
    }

    /**
     * Determine if the last payment is rejected.
     */
    public function getIsRejectedPaymentAttribute(): bool
    {
        $lastPayment = $this->payments()->latest('created_at')->first();

        return $lastPayment?->status === PaymentStatus::REJECTED;
    }

    /**
     * Model booted events.
     *
     * - Cascade soft deletes to related SaleItems and Payments.
     */
    public static function booted()
    {
        // static::creating(function ($sale) {
        //     $sale->invoice_number = self::generateInvoiceNumber();
        // });

        static::deleting(function ($sale) {
            $sale->saleItems->each->delete();
            $sale->payments->each->delete();
        });
    }
}
