<?php

namespace App\Notifications\Admin\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;
class ProjectStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $project;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($project)
    {
        $this->project = $project;
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
                    ->line($this->project->name." status changed to ".$this->project->project_status->name)
                    ->action('Notification Action', url('/admin/project/'.$this->project->id))
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
            'notification' => $this->project->name." status changed to".$this->project->project_status->name,
            'not_id'   => $this->project->id,
            'not_type' => 'project',
            'object_id' => $notifiable->id,
        ];
    }



    public function toBroadcast($notifiable)
    {
        $notification = $this->project->name." status changed to ".$this->project->project_status->name;
        return (new ProjectEvent($notifiable->id,$notification,$this->project->id,'project'));
    }

}
