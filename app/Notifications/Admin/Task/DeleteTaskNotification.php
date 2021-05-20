<?php

namespace App\Notifications\Admin\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class DeleteTaskNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $task_title,$current_user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($task_title,$current_user)
    {
        $this->task_title = $task_title;
        $this->current_user = $current_user;
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
                    ->line($this->task_title." task is deleted by ".$this->current_user->name)
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
            'notification'     => $this->task_title." task is deleted by ".$this->current_user->name,
        ];
    }



    public function toBroadcast($notifiable)
    {
        $notification = $this->task_title." task is deleted by ".$this->current_user->name;
        return (new ProjectEvent($notifiable->id,$notification,null,'task'));
    }

}
