<?php

namespace App\Notifications\Admin\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class DeleteClientNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $client,$admin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($client,$admin)
    {
        $this->client = $client;
        $this->admin  = $admin;
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
        ->line("Client ".$this->client." removed by ".$this->admin->name);
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
            'notification' => "Client ".$this->client." removed by ".$this->admin->name,
        ];
    }


    public function toBroadcast($notifiable)
    {


        $notification = "Client ".$this->client." removed by ".$this->admin->name;
        return (new ProjectEvent($notifiable->id,$notification,null,'profile'));
    }
}
