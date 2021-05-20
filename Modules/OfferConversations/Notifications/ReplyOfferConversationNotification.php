<?php

namespace Modules\OfferConversations\Notifications;

use Illuminate\Bus\Queueable;
use App\Notifications\BaseNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\OfferConversations\Entities\OfferConversation;

class ReplyOfferConversationNotification extends BaseNotification
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
    public function __construct(OfferConversation $offerConversation, $user, $description, $subject)
    {
      $this->offerConversation = $offerConversation;
      $this->user = $user;
      $this->description = $description;
      $this->subject = $subject;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
                    ->view('emails.offer-reply', ['notifiable' => $notifiable, 'description' => $this->description, 'offerConversation' => $this->offerConversation])
                    ->subject('##Offer-' . $this->offerConversation->offer->number . '## ' . $this->subject);
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
            //
        ];
    }
}
