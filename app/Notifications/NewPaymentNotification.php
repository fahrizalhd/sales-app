<?php

namespace App\Notifications;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPaymentNotification extends Notification
{
    use Queueable;

    public Payment $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Store notification in the database.
     */
    public function toDatabase(object $notifiable): array
    {
        $status = $this->payment->status instanceof PaymentStatus ? $this->payment->status : PaymentStatus::from($this->payment->status);
        $sale = $this->payment->sale;

        return [
            'message'   => sprintf('A new payment of Rp%s via %s has been made for invoice %s (Status: %s).',
                number_format($this->payment->amount, 0, ',', '.'),
                strtoupper($this->payment->method->value),
                $sale?->invoice_number ?? '-',
                $status->label()
            ),
            'payment_id' => $this->payment->id,
        ];
    }
}
