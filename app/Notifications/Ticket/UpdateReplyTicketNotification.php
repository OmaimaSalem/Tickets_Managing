<?php

namespace App\Notifications\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Notifications\BaseNotification;

use Modules\TicketComment\Entities\TicketComment;

class UpdateReplyTicketNotification extends BaseNotification
{
    use Queueable;

    private $ticketComment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TicketComment $ticketComment)
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
        $cc = [];
        if($this->ticketComment->ticket->mails){
            foreach ($this->ticketComment->ticket->mails as $mail) {
                $cc[] = $mail->email;
            }
        }



        if($this->ticketComment->ticket->owner->count() > 0) {
            foreach($this->ticketComment->ticket->owner as $owner){
                $cc[] = $owner->email;
            }
        }



        $message = (new MailMessage)
                    ->view('emails.update-ticket-reply', ['ticketComment' => $this->ticketComment,'notifiable' => $notifiable])
                    ->subject(__('Mail/Ticket/UpdateReplyTicketNotification.subject'))
                    ->cc($cc)
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
