<?php

namespace App\Notifications\Ticket;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Modules\TicketComment\Entities\TicketComment;

class CommentTicketNotification extends BaseNotification
{
    use Queueable;

    private $ticketComment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ticketComment)
    {
        $this->ticketComment = $ticketComment;
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
        $message = (new MailMessage)
                    ->view('emails.ticketcommentReply', ['ticketComment' => $this->ticketComment,'notifiable' => $notifiable])
                    ->subject(__('Mail/Ticket/CommentTicketNotification.subject'))
                    ->bcc(config('mail.bcc'));




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
