<?php

namespace App\Notifications\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ticket;
class ClientReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $ticket,$reply;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket,$reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
                    ->subject("client replied to ".$this->ticket->name)
                    ->view('emails.client-ticket-reply', ['notifiable' => $notifiable,'reply' => $this->reply,'ticket' => $this->ticket]);
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
            'notification'     => "client replied to ".$this->ticket->name,
            'not_id'   => $this->ticket->id,
            'not_type' => 'ticket',
            'object_id' => $notifiable->id
        ];

    }
}
