<?php

namespace App\Notifications\Admin\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class StatusTaskNotification extends Notification implements ShouldQueue
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
        // return ['mail','database','broadcast'];
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
                    ->line("Task ".$this->task->name." status changed to ".$this->task->task_status->name." by ".$this->current_user->name)
                    ->action($this->task->name, url('/admin/task/'.$this->task->id))
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
            'notification'     => "Task ".$this->task->name." status changed to ".$this->task->task_status->name." by ".$this->current_user->name,
            'not_id'   => $this->task->id,
            'not_type' => 'task',
            'object_id' => $notifiable->id
        ];
    }



    public function toBroadcast($notifiable)
    {
        $notification = "Task ".$this->task->name." status changed to ".$this->task->task_status->name." by ".$this->current_user->name;
        return (new ProjectEvent($notifiable->id,$notification,$this->task->id,'task'));
    }


}
