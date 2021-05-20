<?php

namespace Modules\Offer\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\BaseNotification;
use Modules\Offer\Entities\Offer;

class OfferCreatedNotification extends BaseNotification
{
    use Queueable;
    private $offer, $user, $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Offer $offer, $user, $data)
    {
        $this->offer = $offer;
        $this->user = $user;
        $this->data = $data;
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
                    ->view('emails.offer-created', ['offer' => $this->offer, 'notifiable' => $notifiable, 'user' => $this->user])
                    ->subject(__('Mail/Offer/OfferNotification.subject') . $this->offer->number)
                    ->attach(storage_path('app/Tecsee_AGB.pdf'), ['mime' => 'application/pdf'])
                    ->attachData($this->data['pdf']->output(), $this->data['title'], [
                        'as' => $this->data['title'],
                        'mime' => 'application/pdf',
                    ]);
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
