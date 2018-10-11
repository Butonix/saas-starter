<?php

namespace App\Notifications\System;

use App\Models\System\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TenantCreated extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($user)
    {
        return (new MailMessage)
            ->subject(__(':name Invitation', ['name' => config('app.name')]))
            ->greeting(__('Hello :name', ['name' => $user->name]))
            ->line(__('To get started you need to set a password.'))
            ->action(__('Set Password'), "https://{$user->website->hostnames->first()->fqdn}/password/reset/{$this->token}");
    }
}
