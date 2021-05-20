<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    Report for {{ date('Y-m-d') }}
    <table border="1px" >

        <thead>
            <td>
                Perpose
            </td>
            <td>
                Data
            </td>
        </thead>
        @foreach($data as $key => $details)
         @if(count($details['data']) <= 0) @continue @endif
         <tr>
            <td>{{ $details['title'] }}</td>
            <td>
                <ul>
                    @foreach ($details['data'] as $item)
                        <li>
                            <span style="color:red">{{ $item->name }}</span>
                            @switch($key)
                                @case('highworktime')
                                        <span style="color:red">{{ ($item->attendes[0]->day_time/60) }}</span>
                                    @break
                                @case('lowworktime')
                                        <span style="color:red"> {{ ($item->attendes[0]->day_time/60) }}</span>
                                    @break

                                    @case('nobreaks')
                                        <span style="color:red">has no breaks today</span>
                                    @break

                                    @case('unclosedbreak')
                                        <span style="color:red">forgot to close one or more of his breaks</span>
                                    @break
                                @default
                            @endswitch
                        </li>
                    @endforeach
                </ul>
            </td>
          </tr>
        @endforeach
    </table>
</body>
</html>
