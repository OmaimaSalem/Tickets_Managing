@extends('emails.layout')

@section('content')
<p> Name: {{ $name }} </p>
<p> E-mail: {{ $email }} </p>
<p> Telefonnummer: {{ $mobile }} </p>
<p> Nachricht: {{ $comment }} </p>
@endsection