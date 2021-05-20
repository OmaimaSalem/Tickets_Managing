<table>
    <thead>
        <tr>
            <th>Project name</th>
            <th>Client</th>
            <th>Actual Time</th>
            <th>Remaning Time</th>
            <th>Budget</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $project)
        <tr>
            <td align="left" valign="top">{{ $project->name }}</td>
            <td align="left" valign="top">
                @foreach ($project->owners as $owner)
                {{  $owner->name }}<br />
                @endforeach

            </td>
            <td align="left" valign="top">
                {{  $project->project_actual_time }}<br />
            </td>
            <td align="left" valign="top">
                {{  $project->project_remaining_time }}<br />
            </td>
            <td align="left" valign="top">{{ $project->budget }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
