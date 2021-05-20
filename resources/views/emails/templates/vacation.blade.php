<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body>
    <div style="font-size:14px;">
        <div style="font-weight:bold">user <span style="color:red">{{ $vacation->user->name}}</span> <br />
        asks for vacation <br />
        from <span style="color:red">{{ $vacation->day_from }} {{ $vacation->time_from }}</span><br/>
        to <span style="color:red">{{ $vacation->day_to }} {{ $vacation->time_to }}</span><br/>
        reason  <span style="color:red">{{ $vacation->reason }}</span><br/></div>

        @if($vacation->sick_vacation == true)
            <a href="{{ url(\Storage::URL('sick_images/'.$vacation->sick_image)) }}"><img height="100px" width="100px" src="{{ url(\Storage::URL('sick_images/'.$vacation->sick_image)) }}" /></a>
        @endif
    </div>
    {{-- {{ $vacation}} --}}
</body>
</html>
