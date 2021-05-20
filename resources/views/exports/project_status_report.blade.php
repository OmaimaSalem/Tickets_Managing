<table>
    <thead>
        <tr>
            <th>Project name</th>
            <th>Open</th>
            <th>Inprogress</th>
            <th>Pending</th>
            <th>Done</th>
            <th>AssignTo</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $project)
        <tr>
            <td align="left" valign="top">{{ $project->name }}</td>
            <td align="left" valign="top">

                @foreach (collect($project->tickets)->filter(function($ticket){
                return $ticket->status_id == 1;
                }) as $ticket)
                {{  $ticket->name }}<br />
                @endforeach

                @foreach (collect($project->tasks)->filter(function($task){
                return $task->status_id == 1;
                }) as $task)
                {{  $task->name }}<br />
                @endforeach

            </td>
            <td align="left" valign="top">
                @foreach (collect($project->tickets)->filter(function($ticket){
                return $ticket->status_id == 3;
                }) as $ticket)
                {{  $ticket->name }}<br />
                @endforeach

                @foreach (collect($project->tasks)->filter(function($task){
                return $task->status_id == 3;
                }) as $task)
                {{  $task->name }}<br />
                @endforeach </td>
            <td align="left" valign="top">
                @foreach (collect($project->tickets)->filter(function($ticket){
                return $ticket->status_id == 2;
                }) as $ticket)
                {{  $ticket->name }}<br />
                @endforeach

                @foreach (collect($project->tasks)->filter(function($task){
                return $task->status_id == 2;
                }) as $task)
                {{  $task->name }}<br />
                @endforeach
            </td>
            <td align="left" valign="top">
                @foreach (collect($project->tickets)->filter(function($ticket){
                return $ticket->status_id == 4;
                }) as $ticket)
                {{  $ticket->name }}<br />
                @endforeach

                @foreach (collect($project->tasks)->filter(function($task){
                return $task->status_id == 4;
                }) as $task)
                {{  $task->name }}<br />
                @endforeach

            </td>
            <td align="left" valign="top">
                @foreach ($project->project_assign as $assign)
                {{  $assign->name }}<br />
                @endforeach
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
