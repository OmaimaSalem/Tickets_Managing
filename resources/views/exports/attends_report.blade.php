<table>
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Date</th>
            <th>Entry</th>
            <th>Exit</th>
            <th>Entry late</th>
            <th>Entry early</th>
            <th>Exit late</th>
            <th>Exit early</th>
            <th>Break</th>
            <th>Work time</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $key => $item)
        <tr>
            <td align="left" valign="top">{{ $item->name }}</td>
            <td align="left" valign="top">{{ $item->date }}</td>
            <td align="left" valign="top">{{ $item->entry }}</td>
            <td align="left" valign="top">{{ $item->exit }}</td>
            <td align="left" valign="top">{{ $item->entry_late }}</td>
            <td align="left" valign="top">{{ $item->entry_early }}</td>
            <td align="left" valign="top">{{ $item->exit_late }}</td>
            <td align="left" valign="top">{{ $item->exit_early }}</td>
            <td align="left" valign="top">{{ $item->break }}</td>
            <td align="left" valign="top">{{ $item->work_time }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
