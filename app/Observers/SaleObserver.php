<?php

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\NewSaleNotification;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        $admins = User::where('role', UserRole::ADMIN)->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewSaleNotification($sale));
        }
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        //
    }
}
