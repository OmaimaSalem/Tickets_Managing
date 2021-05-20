<style>
    table,
    td,
    tr {
        border: 1px solid black;
        width: 70%;
        margin: auto;
        padding: 10px;
    }
</style>
<br /><br />
<table>
    <tr>
        <td>
            This is a working employee Report for :-&nbsp;&nbsp;
        </td>
        <td>
            {{ $data['name'] }}
        </td>
    </tr>

    @if(isset($data['from']) && trim($data['from']) != "")
    <tr>
        <td>
            from :-&nbsp;&nbsp;
        </td>
        <td>
            {{ $data['from'] }}
        </td>
    </tr>
    @endif

    @if(isset($data['to']) && trim($data['to']) != "")
    <tr>
        <td>
            to :-&nbsp;&nbsp;
        </td>
        <td>
            {{ $data['to'] }}
        </td>
    </tr>
    @endif

</table>
<br />

@if(count($data['attendes']) > 0)
<table>
    <tr>
        <td>Day</td>
        <td>Working hours</td>
        <td>Total breaks</td>
        <td>Total work</td>
        <td>Need Approve</td>
    </tr>
    @foreach ($data['attendes'] as $attend)
    <tr>
        <td>{{ $attend['date'] }}</td>
        <td>{{ ($attend['day_time'] / 60) }}</td>
        <td>{{ ($attend['breaks_total'] / 60) }}</td>
        <td>{{ (($attend['day_time'] - $attend['breaks_total']) / 60)  }}</td>
        <td>{{ $attend['active'] ? "No" : "Yes"  }}</td>
    </tr>
    @endforeach
</table>
@endif
<br />

<table>
    <tr>
        <td>
            Total Work for that Date is :-&nbsp;&nbsp;
        </td>
        <td>
            {{ (($data['day_time_total'] - $data['break_time_total']) / 60) }} Hour
        </td>
    </tr>
</table>
