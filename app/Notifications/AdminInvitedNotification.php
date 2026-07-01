<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminInvitedNotification extends Notification
{
    use Queueable;

    public function __construct(public User $user)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Employee Payout Tracker — Admin Invitation')
            ->greeting('Hello ' . $this->user->name . ',')
            ->line('You have been added as an ' . $this->user->role->value . ' in the Employee Payout Tracker.')
            ->line('Your registered email is: ' . $this->user->email)
            ->line('Please use the forgot password flow to set your secure password and access the dashboard.')
            ->action('Access Dashboard', url('/login'))
            ->line('Thank you for using our application!');
    }
}
