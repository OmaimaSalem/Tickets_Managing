<?php

namespace App\Notifications\Admin\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class TicketEventNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $event;
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
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
        //return ['mail','database','broadcast'];
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
                    ->line("Ticket ".$this->event['ticket']['title']." has Event '".$this->event['title']."' today")
                    ->action($this->event['ticket']['title'], url('/admin/ticket/'.$this->event['ticket']['id']))
                    ->line('Thank you for using our application!');
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
            'notification'  => "Ticket ".$this->event['ticket']['title']." has Event '".$this->event['title']."' today",
            'not_id'   => $this->event['ticket']['id'],
            'not_type' => 'ticket',
            'object_id' => $notifiable->id
        ];
    }

    public function toBroadcast($notifiable)
    {
        $notification = "Ticket ".$this->event['ticket']['title']." has Event '".$this->event['title']."' today";
        return (new ProjectEvent($notifiable->id,$notification,$this->event['ticket']['id'],'ticket'));
    }

}
