@extends('emails.layout')

@section('content')

<p>
  <h3 style="color: #3b73af; font-weight: 800">{!! __('Mail/Ticket/CommentTicketNotification.commentTicket', ['ticketNumber' =>
    $ticketComment->ticket->number]) !!}</h3>
</p>

<table role="presentation" border="0" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td colspan="2" style="border: 1px solid #d8d8d8; background: #f5f5f5; padding: 20px; border-radius: 10px;">{!!
        $ticketComment->content !!}</td>
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
              <td> <a href="{{ url('/admin/ticket/'.$ticketComment->ticket->id) }}"
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