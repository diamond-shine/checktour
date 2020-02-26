<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Shelter\Guard\Models\UserInvite;

class InviteUser extends Notification
{
    /**
     * @return array
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * @param UserInvite $invite
     * @return MailMessage
     */
    public function toMail(UserInvite $invite): MailMessage
    {
        return (new MailMessage)
            ->subject(
                _('Запрошення')
            )
            ->greeting(
                _('Привіт')
            )
            ->line(
                _('Вас було запрошено до адміністрування сайтом')
            )
            ->action(
                'Перейти до створення профілю',
                route('control.auth.invite', $invite->token)
            );
    }
}
