<?php

namespace App\Notifications\Admin\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class AssigTaskNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $task,$current_user,$assign_name;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($task,$current_user,$assign_name)
    {
        $this->task         = $task;
        $this->current_user = $current_user;
        $this->assign_name      = $assign_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
        //return ['mail','database','broadcast'];
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
                    ->line($this->assign_name." is assigned to ".$this->task->name." task by ".$this->current_user->name)
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
            'notification'     => $this->assign_name." is assigned to ".$this->task->name." task by ".$this->current_user->name,
            'not_id'   => $this->task->id,
            'not_type' => 'task',
            'object_id' => $notifiable->id,
        ];
    }


    public function toBroadcast($notifiable)
    {
        $notification = $this->assign_name." is assigned to ".$this->task->name." task by ".$this->current_user->name;
        return (new ProjectEvent($notifiable->id,$notification,$this->task->id,'task'));
    }

}
