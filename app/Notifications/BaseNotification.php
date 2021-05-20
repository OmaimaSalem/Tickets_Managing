<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BaseNotification extends Notification
{
    public function intro($message, $notifiable)
    {
        if ($notifiable->metadata) {
            $name = $notifiable->metadata->last_name ? $notifiable->metadata->last_name : $notifiable->name;
            if ($notifiable->metadata->gender == 'male') {
                $message->line(__('Mail/Intro.male', ['name' => $name]));
            } elseif ($notifiable->metadata->gender == 'female') {
                $message->line(__('Mail/Intro.female', ['name' => $name]));
            } else {
                $message->line(__('Mail/Intro.unknown', ['name' => $name]));
            }
        } else {
            $message->line(__('Mail/Intro.unknown', ['name' => $notifiable->name])); 
        }

        return $message;
    }


    
}
