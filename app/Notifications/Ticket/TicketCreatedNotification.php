<?php

namespace App\Notifications\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Ticket;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.'.$this->ticket->name)
                    ->action('Notification Action', url('/admin/ticket/'. $this->ticket->id))
                    ->line('Thank you for using our application!');


        // $message = (new MailMessage)
        //         ->subject(__('Mail/Ticket/TicketChangeStatusNotification.subject'))
        //         ->bcc(config('mail.bcc'));

        // $message = $this->intro($message, $notifiable);

        // $message->line(__('Mail/Ticket/TicketChangeStatusNotification.ticketName', ['ticket_name' => $this->ticket->name, 'status' => __('Mail/Ticket/TicketChangeStatusNotification.status.'.$this->ticket->ticket_status->name)]))
        //         ->action(__('Mail/Ticket/TicketChangeStatusNotification.seeMore'), url('/admin/ticket/'. $this->ticket->id))
        //         ->line(__('Mail/Ticket/TicketChangeStatusNotification.footer'));


        return $message;

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
