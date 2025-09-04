<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case SUCCESS = 'SUCCESS';
    case PENDING = 'PENDING';
    case FAILED = 'FAILED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::SUCCESS   => 'Success',
            self::PENDING   => 'Pending',
            self::FAILED    => 'Failed',
            self::REJECTED  => 'Rejected',
        };
    }   
}