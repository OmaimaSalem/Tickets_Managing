<?php

namespace App\Notifications\Admin\Project;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\BaseNotification;
use App\Models\Project;
use App\Events\Admin\ProjectEvent;

class ProjectAssign extends BaseNotification implements ShouldQueue
{
    use Queueable;
    private $project,$current_user,$assigns;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Project $project,$current_user,$assigns)
    {
        $this->project = $project;
        $this->current_user = $current_user;
        $this->assigns = $assigns;
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


        $message = (new MailMessage)
        ->subject(__('Mail/Project/ProjectAssignNotification.subject'))
        ->bcc(config('mail.bcc'));

        $message = $this->intro($message, $notifiable);

        $message->line(__('Mail/Project/ProjectAssignNotification.projectName', ['project_name' => $this->project->name]))
        ->line(__('Mail/Project/ProjectAssignNotification.description', ['description' => $this->project->description]))
        ->line(__('Mail/Project/ProjectAssignNotification.owner', ['owner' => $this->project->owner ? $this->project->owner->first()->name : '']))
        ->action(__('Mail/Project/ProjectAssignNotification.seeMore'), url('/admin/project/'. $this->project->id))
        ->line(__('Mail/Project/ProjectAssignNotification.footer'));

        return $message;

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
            'notification'     => $this->current_user->name." added ".$this->project->name." to ".$notifiable->name,
            'not_id'   => $this->project->id,
            'not_type' => 'project',
            'object_id' => $notifiable->id
        ];
    }


    public function toBroadcast($notifiable)
    {
        $notification = $this->current_user->name." added ".$this->project->name." to ".implode(', ',$this->assigns->map(function($assign){
            return $assign->name;
        })->toArray());
        return (new ProjectEvent($notifiable->id,$notification,$this->project->id,'project'));
    }
}
