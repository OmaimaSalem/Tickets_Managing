<table>
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Vacation Description</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vacations as $vacation)
        <tr>
            <td align="left" valign="top">{{ $vacation->user->name }}</td>
            <td align="left" valign="top">
                {{ $vacation->reason ? $vacation->reason : '-----' }}

            </td>
            <td align="left" valign="top">
                {{ $vacation->day_from ? $vacation->day_from : '-----' }} </td>
            <td align="left" valign="top">
                {{ $vacation->day_to ? $vacation->day_to : '-----' }} </td>
            <td align="left" valign="top">
                {{ $vacation->status == "review" ? 'Not Approved' : 'Approved' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
