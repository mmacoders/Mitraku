<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class ResetPassword extends BaseResetPassword
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['gmail'];
    }

    /**
     * Get the mail representation of the notification for Gmail channel.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toGmail($notifiable)
    {
        $resetUrl = $this->resetUrl($notifiable);
        
        $subject = Lang::get('Reset Password Notification');
        
        // Render the HTML email template
        $body = View::make('emails.password-reset', [
            'user' => $notifiable,
            'resetUrl' => $resetUrl,
            'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
        ])->render();

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}