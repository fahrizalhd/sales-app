<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case SUCCESS = 'SUCCESS';
    case PENDING = 'PENDING';
    case REJECTED = 'REJECTED';
    case REFUNDED = 'REFUNDED';

    public function label(): string
    {
        return match ($this) {
            self::SUCCESS   => 'Success',
            self::PENDING   => 'Pending',
            self::REJECTED  => 'Rejected',
            self::REFUNDED  => 'Refunded',
        };
    }   
}