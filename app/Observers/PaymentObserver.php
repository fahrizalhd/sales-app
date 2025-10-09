<?php

namespace App\Observers;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\NewPaymentNotification;
use App\Notifications\PaymentApprovalNotification;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $admins = User::where('role', UserRole::ADMIN)->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewPaymentNotification($payment));
        }
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        if ($payment->isDirty('status')) {
            $oldStatus = $payment->getOriginal('status');
            $oldStatusEnum = $oldStatus instanceof PaymentStatus ? $oldStatus : PaymentStatus::from($oldStatus);
            $newStatus = $payment->status;
            $newStatusEnum = $newStatus instanceof PaymentStatus ? $newStatus : PaymentStatus::from($newStatus);
            if (in_array($newStatusEnum, [PaymentStatus::SUCCESS, PaymentStatus::REJECTED, PaymentStatus::REFUNDED])) {
                $admins = User::where('role', UserRole::ADMIN)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new PaymentApprovalNotification($payment, $oldStatusEnum, $newStatusEnum));
                }
            }
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
