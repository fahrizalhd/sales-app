<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case QRIS = 'QRIS';
    case CASH = 'CASH';
    case DEBIT = 'DEBIT';

    public function label(): string
    {
        return match ($this) {
            self::QRIS  => 'QRIS',
            self::CASH  => 'Cash',
            self::DEBIT => 'Debit',
        };
    }   
}