<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use Blameable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = 
    [
        'sale_id', 
        'method', 
        'amount', 
        'status', 
        'payment_reference',
        'approved_at',
        'rejected_at',
    ];

     /**
     * The attributes that should be cast to native types.
     */
    protected $casts = 
    [
        'method'        => PaymentMethod::class,
        'status'        => PaymentStatus::class,
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
    ];

     /**
     * Get the sale for this payment.
     */
    public function sale() 
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the user who created the payment.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the payment.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
