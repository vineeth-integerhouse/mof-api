<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends ResetPassword
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
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
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }
        $email = urlencode($notifiable->email);

        if ($notifiable->role_id == 3) {
            $url= config("auth.vet_reset_password_base_url") . $this->token . '&email=' . urlencode($notifiable->email);
          
        } elseif ($notifiable->role_id  == 4) {
            $url=   config("auth.reset_password_base_url") . $this->token . '&email=' . urlencode($notifiable->email);

        } else {
            $url=config("auth.admin_reset_password_base_url") . $this->token . '&email=' . urlencode($notifiable->email);
     
        }
        
        $body= "You are receiving this email because we received a password reset request for your account.";
       
        return (new MailMessage)
            ->subject(__('passwords.email_password_reset_request_subject'))
            ->view(
                'emails.reset_password',
                ['name'=>$notifiable->first_name,'url' => $url, 'body' => $body]
            );
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
