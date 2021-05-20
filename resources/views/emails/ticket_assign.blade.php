@extends('emails.layout')

@section('content')

<p>
  <h2 style="color: #3b73af; font-weight: light">{!! __('Mail/Ticket/TicketAssign.intro') !!}</h2>
  <a href="{{ url('admin/ticket', $ticket->id)  }}">{!! $ticket->number !!}-{!! $ticket->name !!}</a>
</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td colspan="2" style="border: 1px solid #d8d8d8; background: #f5f5f5; padding: 20px; border-radius: 10px;">{!!
        $ticket->description !!}</td>
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
              <td> <a href="{{ url('/admin/ticket/'.$ticket->id) }}"
                  target="_blank">{{__('Mail/Task/TaskAssignNotification.seeMore')}}</a> </td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<p>{{__('Mail/Task/TaskAssignNotification.footer')}}</p>
@endsection