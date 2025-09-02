<?php

namespace App\Enums;

enum SaleStatus: string
{
    case UNPAID = 'UNPAID';
    case PARTIALLY_PAID = 'PARTIALLY_PAID';
    case NEED_REVIEW = 'NEED_REVIEW';
    case PAID = 'PAID';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID            => 'Unpaid',
            self::PARTIALLY_PAID    => 'Partially Paid',
            self::NEED_REVIEW       => 'Need Review',
            self::PAID              => 'Paid',
            self::CANCELLED         => 'Cancelled',
        };
    }   
}