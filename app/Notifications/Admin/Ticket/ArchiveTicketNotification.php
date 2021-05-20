<?php

namespace App\Notifications\Admin\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class ArchiveTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $ticket,$current_user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticket,$current_user)
    {
        $this->ticket = $ticket;
        $this->current_user = $current_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        //return ['mail','database','broadcast'];
        return ['database','broadcast'];

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
                    ->line($this->ticket->name." is added in archive list by ".$this->current_user->name)
                    ->action($this->ticket->name, url('admin/ticket/'.$this->ticket->id))
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
            'notification'=> $this->ticket->name." is added in archive list by ".$this->current_user->name,
            'not_id'   => $this->ticket->id,
            'not_type' => 'ticket',
            'object_id' => $notifiable->id
        ];
    }



    public function toBroadcast($notifiable)
    {
        $notification = $this->ticket->name." is added in archive list by ".$this->current_user->name;
        return (new ProjectEvent($notifiable->id,$notification,$this->ticket->id,'ticket'));
    }

}
