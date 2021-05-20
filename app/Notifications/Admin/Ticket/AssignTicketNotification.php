<?php

namespace App\Notifications\Admin\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class AssignTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $ticket,$current_user,$assigns;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticket,$current_user,$assigns)
    {
        $this->ticket = $ticket;
        $this->current_user = $current_user;
        $this->assigns = implode(', ',$assigns->map(function($assign){
            return $assign->name;
        })->toArray());
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
                    ->line($this->current_user->name." added ".$this->ticket->name." to ".$this->assigns)
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
            'notification'=> $this->current_user->name." added ".$this->ticket->name." to ".$this->assigns,
            'not_id'   => $this->ticket->id,
            'not_type' => 'ticket',
            'object_id' => $notifiable->id
        ];
    }


    public function toBroadcast($notifiable)
    {
        $notification = $this->current_user->name." added ".$this->ticket->name." to ".$this->assigns;
        return (new ProjectEvent($notifiable->id,$notification,$this->ticket->id,'ticket'));
    }

}
