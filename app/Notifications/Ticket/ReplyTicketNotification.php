<?php

namespace App\Notifications\Ticket;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Modules\TicketComment\Entities\TicketComment;

class ReplyTicketNotification extends BaseNotification
{
    use Queueable;

    private $ticketComment;
    public $user;
    public $description;
    public $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TicketComment $ticketComment, $user, $description, $subject)
    {
        $this->ticketComment = $ticketComment;
        $this->user = $user;
        $this->description = $description;
        $this->subject = $subject;
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
        $message = (new MailMessage)
                    ->view('emails.ticket-reply', ['ticketComment' => $this->ticketComment,'notifiable' => $notifiable, 'user' => $this->user, 'description' => $this->description])
                    ->subject($this->subject)
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
