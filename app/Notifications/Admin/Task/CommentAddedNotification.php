<?php

namespace App\Notifications\Admin\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class CommentAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $task,$current_user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($task,$current_user)
    {
        $this->task = $task;
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
                    ->line($this->current_user->name." commented on ".$this->task->name." task")
                    ->action($this->task->name, url('admin/ticket/'.$this->task->id))
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
            'notification'=> $this->current_user->name." commented on ".$this->task->name." task",
            'not_id'   => $this->task->id,
            'not_type' => 'task',
            'object_id' => $notifiable->id
        ];
    }


    public function toBroadcast($notifiable)
    {
        $notification = $this->current_user->name." commented on ".$this->task->name." task";
        return (new ProjectEvent($notifiable->id,$notification,$this->task->id,'task'));
    }
}
