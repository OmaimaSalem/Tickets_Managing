<?php

namespace App\Notifications\Admin\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Project;
use App\Events\Admin\ProjectEvent;
class ProjectOwnerAssign extends Notification implements ShouldQueue
{
    use Queueable;
    private $project,$owners,$current_user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $project,$current_user,$owners)
    {
        $this->project = $project;
        $this->owners  = implode(', ',$owners->map(function($owner){
            return $owner->name;
        })->toArray());

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
                    ->line('client '.$this->owners." assigned to ".$this->project->name)
                    ->action($this->project->name, url('/admin/project/'.$this->project->id))
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
            'notification' => 'client '.$this->owners." assigned to ".$this->project->name,
            'not_id'   => $this->project->id,
            'not_type' => 'project',
            'object_id' => $notifiable->id,
        ];
    }



    public function toBroadcast($notifiable)
    {
        $notification = 'client '.$this->owners." assigned to ".$this->project->name;
        return (new ProjectEvent($notifiable->id,$notification,$this->project->id,'project'));
    }

}
