<table>
    <thead>
        <tr>
            <th>Employee name</th>
            <th>Project name</th>
            <th>Ticket name</th>
            <th>Task name</th>
            <th>Total hours</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td valign="top">{{ $user->name }}</td>
            <td valign="top">
                @foreach ($user->time_projects as $project)
                {{  $project->name }}<br />
                @endforeach

            </td>
            <td valign="top">
                @foreach ($user->tickets as $ticket)
                {{  $ticket->name }}<br />
                @endforeach
            </td>
            <td valign="top">
                @foreach ($user->tasks as $task)
                {{  $task->name }}<br />
                @endforeach
            </td>
            <td valign="top">{{ $user->total_time }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
