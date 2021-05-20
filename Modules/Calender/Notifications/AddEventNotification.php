<?php

namespace Modules\Calender\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use \App\Events\Admin\ProjectEvent;
class AddEventNotification extends Notification
{
    use Queueable;
    protected $event;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'notification' => 'client '.$this->event->name." added event to you",
            'not_id'   => null,
            'not_type' => 'calendar',
            'object_id' => $notifiable->id,
        ];

    }



    public function toBroadcast($notifiable)
    {
        $notification = 'client '.$this->event->name." added event to you";
        return (new ProjectEvent($notifiable->id,$notification,$this->event->id,'project'));
    }
}
