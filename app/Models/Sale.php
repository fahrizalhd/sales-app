<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment;

class Sale extends Model
{
    protected $fillable = ['invoice_number', 'sale_date', 'total_price', 'is_paid'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
