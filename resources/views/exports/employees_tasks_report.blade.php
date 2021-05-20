<table>
    <thead>
        <tr>
            <th>Subject</th>
            <th>Type</th>
            <th>Date From</th>
            <th>Remaning Time</th>
            <th>Time consumed</th>
            <th>Status</th>
            <th>Client</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $key => $item)
        <tr>
            <td align="left" valign="top">{{ $item->name }}</td>
            <td align="left" valign="top">{{ $item->type }}</td>
            <td align="left" valign="top">{{ $item->date_form }}</td>
            <td align="left" valign="top">{{ $item->date_to }}</td>
            <td align="left" valign="top">{{ $item->time_consumed }}</td>
            <td align="left" valign="top">{{ $item->status }}</td>
            <td align="left" valign="top">
                @if(is_array($item->client))
                @foreach ($item->client as $client)
                {{ $client->name }}
                @endforeach
                @else
                -----
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
