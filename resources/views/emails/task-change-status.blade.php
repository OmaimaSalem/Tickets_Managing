@extends('emails.layout')

@section('content')
<strong>
@if ($notifiable->metadata)
    @if ($notifiable->metadata->gender == 'male')
        {{ __('Mail/Intro.male', ['name' => $notifiable->name]) }}
@elseif($notifiable->metadata->gender == 'female')
{{ __('Mail/Intro.female', ['name' => $notifiable->female]) }}
@else
{{ __('Mail/Intro.unknown', ['name' => $notifiable->name]) }}
@endif
@else
{{ __('Mail/Intro.unknown', ['name' => $notifiable->name]) }}
@endif
</strong>
@if($task->project)
<p>
  <h2 style="color: #3b73af; font-weight: light">{!! $task->project->name . ' / ☑ ' . $task->name !!}</h2>
</p>
@endif
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    
    @if($task->project)
    <tr>
      <td width="120">{!! __('Mail/Task/TaskChangeStatusNotification.project') !!}</td>
      <td><a href="{{ url('admin/project', $task->project->id)  }}">{!! $task->project->name !!}</a></td>
    </tr>
    @endif

    <tr>
      <td>{!! __('Mail/Task/TaskChangeStatusNotification.assignee') !!}</td>
      <td><a href="{{ url('/admin/profile', $task->responsible->id)  }}">{!! $task->responsible->name !!}</a></td>
    </tr>
    <tr>
      <td>{!! __('Mail/Task/TaskChangeStatusNotification.status') !!}</td>
      <td><strong>{!! $task->status->name !!}</strong></td>
    </tr>
    <tr>
      <td>{!! __('Mail/Task/TaskChangeStatusNotification.priority') !!}</td>
      <td>{!! $task->priority !!}</td>
    </tr>
    <tr>
      <td>{!! __('Mail/Task/TaskChangeStatusNotification.created_at') !!}</td>
      <td>{!! date('j/M/Y H:m', strtotime($task->created_at)) !!}</td>
    </tr>
    <tr>
      <td>{!! __('Mail/Task/TaskChangeStatusNotification.deadline') !!}</td>
      <td>{{ date('j/M/Y H:m', strtotime($task->deadline)) }}</td>
    </tr>
    <tr>
      <td colspan="2" style="border: 1px solid #d8d8d8; background: #f5f5f5; padding: 20px; border-radius: 10px;">{!!
        $task->description !!}</td>
    </tr>
  </tbody>
</table>
<br>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
  <tbody>
    <tr>
      <td align="center">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td> <a href="{{ url('/admin/task/'.$task->id) }}"
                  target="_blank">{{__('Mail/Task/TaskChangeStatusNotification.seeMore')}}</a> </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<p>{{__('Mail/Task/TaskChangeStatusNotification.footer')}}</p>
@endsection