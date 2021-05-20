Your replay : {!! $ticketComment->comment !!}&nbsp; on ticket : <a
    href="{{ url('admin/ticket/')."/".$ticketComment->ticket->id }}">{{  $ticketComment->ticket->name }}</a>
&nbsp;<div style="color:red">Not sent</div>
