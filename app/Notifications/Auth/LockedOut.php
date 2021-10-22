<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LockedOut extends Notification implements ShouldQueue
{
    use Queueable;
    protected $jailTime;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($jailTime)
    {
        $this->jailTime = $jailTime;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('auth.email_locked_subject'))
            ->line(__("Locked for $this->jailTime"))
            ->line(__('auth.email_locked_line1'))
            ->line(__('auth.email_locked_line2'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
