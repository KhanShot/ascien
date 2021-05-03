<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Psy\Util\Str;

class VerifyEmail extends Notification
{
    use Queueable;
    protected $signedUrl;

    /**
     * Create a new notification instance.
     *
     * @param $signedUrl
     */
    public function __construct($signedUrl)
    {
        $this->signedUrl = $signedUrl;
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

    private function buildUrl($notifiable){
        return URL::temporarySignedRoute(
            'email.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->id,
                'hash' => sha1($notifiable->email),
            ]
        );
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get("email.verify_greeting"))
            ->line(Lang::get('email.verify_description'))
            ->action(Lang::get('email.verify_text'), $this->buildUrl($notifiable))
            ->line(Lang::get("email.verify_except"));
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
