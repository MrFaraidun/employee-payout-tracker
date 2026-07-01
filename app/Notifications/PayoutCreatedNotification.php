<?php

namespace App\Notifications;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Payout $payout)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $employeeName = $this->payout->employee->name;
        $amount = number_format($this->payout->amount_iqd) . ' IQD';

        return (new MailMessage)
            ->subject('New Employee Payout Logged')
            ->line('A new payout has been registered in the system.')
            ->line('Employee: ' . $employeeName)
            ->line('Task: ' . $this->payout->task)
            ->line('Amount: ' . $amount)
            ->line('Status: ' . $this->payout->status->value)
            ->action('View Payout Details', url('/admin/payouts'))
            ->line('Logged by user ID: ' . $this->payout->created_by);
    }
}
