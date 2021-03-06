<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta http-equiv="x-ua-compatible" content="ie=edge">
@guest
@else
  <meta name="user-id" content="{{ Auth::user()->id }}">
@endguest

<title>{{ config('app.name', 'alferp') }}</title>
<link rel="stylesheet" href="{{ mix('dist/css/app.css') }}">