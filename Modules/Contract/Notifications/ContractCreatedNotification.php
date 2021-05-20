<?php

namespace Modules\Contract\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Contract\Entities\Contract;

class ContractCreatedNotification extends Notification
{
    use Queueable;
    private $contract, $user;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Contract $contract, $user)
    {
      $this->contract = $contract;
      $this->user = $user;
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
                  ->view('emails.contract-created', ['contract' => $this->contract, 'notifiable' => $notifiable, 'user' => $this->user])
                  ->subject(__('Mail/Contract/ContractNotification.subject') . $this->contract->number)
                  ->attach(storage_path('app/Tecsee_AGB.pdf'), ['mime' => 'application/pdf']);
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
