<?php

namespace App\Notifications\Admin\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Project;
use App\Events\Admin\ProjectEvent;

class ProjectDueDateNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $project;
    private $target;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $project,$target="todaty")
    {
        $this->project = $project;
        $this->target = $target;
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
                    ->line($this->project->name." due date is ".date('Y-m-d',strtotime($this->project->due_date)))
                    ->action('Notification Action', url('/admin/project'.$this->project->id));
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
            'notification' => $this->project->name." due date is ".date('Y-m-d',strtotime($this->project->due_date)),
            'not_id'   => $this->project->id,
            'not_type' => 'project',
            'object_id' => $notifiable->id
        ];
    }


    public function toBroadcast($notifiable)
    {
        $notification = $this->project->name." due date is ".date('Y-m-d',strtotime($this->project->due_date));
        return (new ProjectEvent($notifiable->id,$notification,$this->project->id,'project'));
    }
}
