<?php

namespace App\Notifications\Admin\User;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Events\Admin\ProjectEvent;

class NewEmployeeNotification extends Notification implements ShouldQueue
{
    use Queueable;
    private $employee,$admin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($employee,$admin)
    {
        $this->employee = $employee;
        $this->admin    = $admin;
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
            $name = $this->admin ? $this->admin->name : 'System';
            return (new MailMessage)
                    ->line("Employee ".$this->employee->name." created by ".$name)
                    ->action($this->employee->name, url('/admin/profile/'.$this->employee->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $name = $this->admin ? $this->admin->name : 'System';
        return [
            'notification' => "Employee ".$this->employee->name." created by ".$name,
            'not_id'   => $this->employee->id,
            'not_type' => 'profile',
            'object_id' => $notifiable->id
        ];
    }


    public function toBroadcast($notifiable)
    {
        $name = $this->admin ? $this->admin->name : 'System';
        $notification = "Employee ".$this->employee->name." created by ".$name;
        return (new ProjectEvent($notifiable->id,$notification,$this->employee->id,'profile'));
    }
}
