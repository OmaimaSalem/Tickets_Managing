<?php

namespace App\Http\Controllers\API;

use App\Models\Status;
use DB;
use App\Http\Requests\TaskRequest\AddTaskRequest;
use App\Http\Requests\TaskRequest\UpdateTaskRequest;
use App\Http\Requests\TaskRequest\ViewTaskRequest;
use App\Http\Requests\TaskRequest\DeleteTaskRequest;
use App\Http\Requests\TaskRequest\ListTaskRequest;
use App\Http\Requests\TaskRequest\FilteTaskRequest;
use App\Http\Requests\TaskRequest\CardTaskRequest;
use App\Models\Task;
use App\Models\User;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\API\BaseController;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotDeletedException;
use App\Http\Resources\Task\TaskResource;
use App\Http\Resources\Task\TaskCollection;


use Illuminate\Http\Request;


use App\Models\Project;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectResource;
use Illuminate\Support\Facades\Storage;


class TaskController extends BaseController
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('permission:task-list|task-create|task-edit|task-delete', ['only' => ['index']]);
      $this->middleware('permission:task-create', ['only' => ['store']]);
      $this->middleware('permission:task-edit', ['only' => ['update']]);
      $this->middleware('permission:task-delete', ['only' => ['destroy']]);
      $this->middleware('permission:task-list', ['only' => ['getTaskCountPerClient', 'getTaskPerClient', 'filterTasks', 'tasksCard']]);
  }

   /**
   * Display a view listing of the resource view.
   *
   * @return Response
   */
  public function index(ListTaskRequest $request)
  {
    $input = $request->validated()['params'];

    if (auth()->user()->isAdmin()) {
      $tasks = Task::with('project.owner', 'ticket', 'responsible', 'task_status');
    } else {
      $taskModel = new Task();
      $tasks = $taskModel->ownTasks(auth()->user()->id);
    }

      // global search
      if (isset($input['global_search']) && $input['global_search']) {
          $input['filters'] = [];
          $tasks->where(function($query) use ($input){
              $query->whereHas('task_status', function($q) use($input) {
                  $q->where('name', 'like', '%'.$input['global_search'].'%');
              });
              $query->orWhereHas('project', function($q) use($input) {
                  $q->where('name', 'like', '%'.$input['global_search'].'%');
              });
              $query->orWhereHas('project.owner', function($q) use($input) {
                  $q->where('name', 'like', '%'.$input['global_search'].'%');
              });
              $query->orWhere('tasks.name','LIKE','%'.$input['global_search'].'%');
              $query->orWhere('tasks.priority','LIKE','%'.$input['global_search'].'%');
              $query->orWhere('tasks.deadline','LIKE','%'.$input['global_search'].'%');
              $query->orWhere('tasks.created_at','LIKE','%'.$input['global_search'].'%');
          });
      }


      $getDone = false;
      // filter
      if (isset($input['filters']) && $input['filters']) {
          foreach ($input['filters'] as $filterObj) {
              // first type of filter
              if ($filterObj['type'] == 'simple') {
                  if($filterObj['name'] == 'number'){
                      $tasks->where('id', 'like', '%'.$filterObj['text'].'%');
                  }
                  else if($filterObj['name'] == 'name'){
                      $tasks->where('name', 'like', '%'.$filterObj['text'].'%');
                  }
                  elseif ($filterObj['name'] == 'owner.name') {
                     $tasks->whereHas('ticket.owner', function($query) use($filterObj) {
                        $query->where('name', 'like', '%'.$filterObj['text'].'%');
                    });
                    $tasks->orwhereHas('project.owner', function($query) use($filterObj) {
                        $query->where('name', 'like', '%'.$filterObj['text'].'%');
                    });
                  }
                  elseif ($filterObj['name'] == 'project.name') {
                      $tasks->orwhereHas('project', function($query) use($filterObj) {
                          $query->where('name', 'like', '%'.$filterObj['text'].'%');
                      });
                  }
                  elseif ($filterObj['name'] == 'ticket.name') {
                      $tasks->orwhereHas('ticket', function($query) use($filterObj) {
                          $query->where('name', 'like', '%'.$filterObj['text'].'%');
                      });
                  }
                  elseif ($filterObj['name'] == 'assigned.name') {
                      if($filterObj['text'] != 'All'){
                          if($filterObj['text'] == 'Not Assigned'){
                              $tasks->has('responsible', '=', 0);
                          }
                          else{
                              $tasks->whereHas('responsible', function($query) use($filterObj) {
                                  $query->where('name', 'like', '%' . $filterObj['text'] . '%') ;
                              });
                          }
                      }
                  }
                  else if($filterObj['name'] == 'deadline'){
                      $tasks->where('deadline', 'like', '%'.$filterObj['text'].'%');
                  }

                  // second type of filter
              }  elseif ($filterObj['type'] == 'select') {
                  // if ($filterObj['name'] == 'status.name') {
                  //     $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                  //     if($status) {
                  //         $tasks->where('tickets.status_id', '=', $status->id);
                  //     }
                  // } 
                  if ($filterObj['name'] == 'status.state') {
                      if (auth()->user()->isAdmin() || auth()->user()->can('ticket-management')) {
                          //  if($filterObj['selected_options'][0] == 'open') {
                          //     $tasks->where('status_id', '=', 1);
                          // } elseif($filterObj['selected_options'][0] == 'pending') {
                          //     $tasks->orWhere('status_id', '=', 2);
                          // } elseif($filterObj['selected_options'][0] == 'in-progress') {
                          //     $tasks->orWhere('status_id', '=', 3);
                          // } elseif($filterObj['selected_options'][0] == 'done') {
                          //   $getDone = true;
                          //     $tasks->orWhere('status_id', '=', 4);
                          // }
                          if( $filterObj['selected_options'][0] == 'open' || 
                              $filterObj['selected_options'][0] == 'pending' || 
                              $filterObj['selected_options'][0] == 'in-progress' || 
                              $filterObj['selected_options'][0] == 'done'){
                                $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                                if($status) {
                                  $tasks->where('tasks.status_id', '=', $status->id);
                                }
                                if($status->id == 4){
                                  $getDone = true;
                                }
                              }
                      } else {
//              $ticketModel = new Ticket();
//              $tickets = $ticketModel->ownTickets(auth()->user()->id)->paginate();
                          //  if($filterObj['selected_options'][0] == 'open') {
                          //     $tasks->where('status_id', '=', 1);
                          // } elseif($filterObj['selected_options'][0] == 'pending') {
                          //     $tasks->orWhere('status_id', '=', 2);
                          // } elseif($filterObj['selected_options'][0] == 'in-progress') {
                          //     $tasks->orWhere('status_id', '=', 3);
                          // } elseif($filterObj['selected_options'][0] == 'done') {
                          //   $getDone = true;
                          //     $tasks->orWhere('status_id', '=', 4);
                          // }
                          if( $filterObj['selected_options'][0] == 'open' || 
                              $filterObj['selected_options'][0] == 'pending' || 
                              $filterObj['selected_options'][0] == 'in-progress' || 
                              $filterObj['selected_options'][0] == 'done'){
                                $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                                if($status) {
                                  $tasks->where('tasks.status_id', '=', $status->id);
                                }
                                if($status->id == 4){
                                  $getDone = true;
                                }
                              }
                      }
                  }
              }
       }
          }
      // sorting
      if (isset($input['sort']) && $input['sort']) {
          foreach ($input['sort'] as $sortObj) {
              if (in_array($sortObj['name'], ['deadline', 'created_at', 'name', 'id'])) {
                  if ($sortObj['order'] == 'desc') {
                      $tasks->latest($sortObj['name']);
                  } else {
                      $tasks->oldest($sortObj['name']);
                  }
              }
          }
      }
      if($getDone == false) {
        $tasks->where('status_id', '!=', 4);
      }


    $tasks = $tasks->latest()->paginate(30);

    return $this->sendResponse(new TaskCollection($tasks), 'Tasks retrieved successfully.');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(AddTaskRequest $request)
  {

      $input = $request->validated();
    $input['created_at'] = Carbon::now();
    $input['created_by'] = auth()->user()->id;
    // $input['owner_id'] = $request->owner_id[0];
    // $input['description'] = $input['description'];
    if($input['estimated_time'] == null) {
      $input['estimated_time'] = 0;
    }

        try {
            $task = Task::create($input);
        } catch (\Throwable $th) {
            throw new ItemNotCreatedException('Task');
        }


    $task = Task::find($task->id);
    return $this->sendResponse(new TaskResource($task->load(['project','responsible','task_status'])), 'Task created successfully.');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show(ViewTaskRequest $request, $id)
  {
    $task = Task::find($id)->load('project.owner', 'ticket', 'responsible', 'task_status');

    if (is_null($task)) {
      throw new ItemNotFoundException($id);
    }

    return $this->sendResponse(new TaskResource($task), 'Task retrieved successfully.');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(UpdateTaskRequest $request, $id)
  {


    $task = Task::find($id);
    $old_status = $task->status_id;
    if (!$task) {
      throw new ItemNotFoundException($id);
    }

    $task->updated_at = Carbon::now();
    $task->updated_by = auth()->user()->id;

    $input = $request->all();
    if(!isset($input['estimated_time']) || $input['estimated_time']  == null) {
      $input['estimated_time'] = 0;
    }


    if(isset($input['description']) && trim($input['description']) != "")
            $input['description'] =  $this->replace_images($input['description'],$id);



     try {
      $updated = $task->update($input);
    } catch (\Throwable $th) {
        dd($th);
      throw new ItemNotUpdatedException('Task');
    }

    if (!$updated)
      throw new ItemNotUpdatedException('Task');

      $max = Task::count();
      $request_to_order = $request->to_index + 1;


      $direction          = $request_to_order > $task->order_column ? 'down' : 'up';
      $step               = $request_to_order - $task->order_column;
      $request_to_order   = $task->order_column + $step;
      $request_from_order = $request->from_index + 1;
      $step               = $step < 0 ? $step * -1 : $step;


    //   if($request->previousTaskId != 0 && $request->fromColumnIndex != $request->toColumnIndex ) {
    //     $dropped_task = Task::find($request->previousTaskId);
    //     $direction          = $request_to_order > $dropped_task->order_column ? 'down' : 'up';
    //     $step               = 1 ;
    //     $request_to_order   = $dropped_task->order_column + 1;
    //   }



      if($request_to_order != $task->order_column){


        if($direction == 'up') {
            $task->update(['order_column' => $request_to_order]);
            if($step <= 1)
            {
                Task::withTrashed()->where('order_column','>',$request_to_order)->where('id','!=',$task->id)->update(['order_column' => DB::raw("GREATEST(order_column - 1, 1)")]);
            }
            $tasks = Task::withTrashed()->where('order_column','>=',$request_to_order)->where('id','!=',$task->id)->increment('order_column',1);
        }elseif($direction == 'down') {
            $task->update(['order_column' => $request_to_order]);
            if($step <= 1)
            {
                Task::withTrashed()->where('order_column','<',$request_to_order)->where('id','!=',$task->id)->increment('order_column',1);
            }
            Task::withTrashed()->where('order_column','<=',$request_to_order)->where('id','!=',$task->id)->update(['order_column' => DB::raw('GREATEST(order_column - 1, 1)')]);
        }



    }
    $task->old_status = $old_status;
    return $this->sendResponse(new TaskResource($task), 'task updated successfully.');
  }




  public function replace_images($description,$id){
    libxml_use_internal_errors(true);
    $doc = new \DOMDocument();
    $doc->loadHTML(mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8'));

    $imgs= $doc->getElementsByTagName('img');
    $count = 1;
    $images = [];
    foreach ($imgs as $img) {
      $img_arr = explode(',',$img->getAttribute("src"));
      if(strpos($img_arr[0],'base64') > -1){
        $images[$count]['content']      = $img_arr[1];
        $images[$count]['extension']    = str_replace(';base64','',explode('/',$img_arr[0])[1]);
      }
      $count++;
    }

    $xpath = new \DOMXPath($doc);
    $count = 1;
    foreach( $xpath->query( '//img') as $img) {
      $img_arr = explode(',',$img->getAttribute("src"));

        if(strpos($img_arr[0],'base64') > -1){
            $new = $doc->createTextNode("[image-$count]"); 
            $img->parentNode->replaceChild($new,$img);
        }
        $count++;
    }
    


    $new_desc = $doc->saveHTML();
    libxml_use_internal_errors(false);
    foreach($images as $key => $image){

      $image_url = '/tasks/' . $id. '/image-'.mt_rand(100000, 999999).".".$image['extension'];
      $attachmentPath = storage_path('app/public'.$image_url);
      $dirName = dirname($attachmentPath);
    
      if (!is_dir($dirName))
          mkdir($dirName, 0755, true);

      $fp = fopen($attachmentPath, "wb");
      file_put_contents($attachmentPath, base64_decode($image['content']));
      fclose($fp);
      $new_desc = str_replace("[image-$key]",'<img src="'.url('storage'.$image_url).'" />',$new_desc);
    }

    return $new_desc;
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(DeleteTaskRequest $request, $id)
  {
    $task = Task::find($id);
    if (is_null($task)) {
      throw new ItemNotFoundException($id);
    }
    if($task->tracking_history->isNotEmpty()) {
      throw new InvalidDataException([
        'tracking_history' => $task->tracking_history->toArray()
      ],
      'Can\'t delete!, someone work in this task.');
    }

    try {
      if(trim($task->description) != ""){
       $images = $this->get_images_from_content($task->description);
        foreach($images as $image_src){
          $image_url = explode("/storage/",$image_src)[1];
          Storage::disk('public')->delete($image_url);
        }
      }

      $task->delete();
    } catch (\Throwable $th) {
       dd($th);
      throw new ItemNotDeletedException('Task');
    }

    return $this->sendResponse(new TaskResource($task), 'Task deleted successfully.');
  }



  public function get_images_from_content($content)
  {
    $content = htmlspecialchars($content);
    $doc = new \DOMDocument();
    $doc->loadHTML($content);
    $xpath = new \DOMXPath($doc);
    $imges = [];
    foreach( $xpath->query( '//img') as $img) {
      $imges[] = $img->getAttribute("src");
    }
    return $imges;
  }



  public function getTasksByTicketId($id, ListTaskRequest $request)
  {
    $tasks = Task::with('project.owner', 'responsible', 'task_status')->whereHas('ticket', function ($query) use ($id) {
      $query->where('id', $id);
    })->latest()->paginate();

    if (is_null($tasks)) {
      throw new ItemNotFoundException($id);
    }

    return $this->sendResponse(new TaskCollection($tasks), 'Tasks retrieved successfully.');
  }

  public function getTasksByProjectId($id, ListTaskRequest $request)
  {
    $tasks = Task::where('project_id', '=', $id)->latest()->get();

    if (is_null($tasks)) {
      throw new ItemNotFoundException($id);
    }

    return $this->sendResponse(TaskResource::collection($tasks), 'Tasks retrieved successfully.');
  }



  public function getTasksCountPerClient($id)
  {
    $user = User::find($id);
    if($user->type == 'client'){
      $tasksNumber = Task::with('project')->whereHas('project.owner', function ($query)  use ($id) {
                        $query->where('owner_id', $id);
                    })->select(DB::Raw('status_id, COUNT(*) as count'))
                    ->groupBy('status_id')->get();
    }
    else{
      $tasksNumber = Task::whereHas('responsible', function ($query)  use ($id) {
                        $query->where('responsible_id', $id);
                    })->select(DB::Raw('status_id, COUNT(*) as count'))
                    ->groupBy('status_id')->get();
    }

    return $this->sendResponse($tasksNumber->toArray(), 'Tasks Number retrieved successfully.');
  }

  public function getTasksPerClient($id)
  {
    $user = User::find($id);
//      return $this->sendResponse($id, 'Tasks owner ');

    if($user->type == 'client'){
//      $tasks = Task::with('project')->whereHas('project.owner', function ($query)  use ($id) {
//                  $query->where('owner_id', $id);
//                })->paginate();


      $tasks = Task::where('owner_id', $id)->with('owner')->with('status')->with('project')->paginate();

      // $tasks = Task::where('owner_id', $id)->paginate();
      return $this->sendResponse($tasks, 'Tasks here ');
    }
    else{
      $tasks = Task::whereHas('responsible', function ($query)  use ($id) {
        $query->where('responsible_id', $id);
      })->paginate();
    }

    return $this->sendResponse(new TaskCollection($tasks), 'Tasks retrieved successfully.');
  }

  public function allCards(Request $request) {

    $tasks = Task::query();
    if($request->project)
    {
      $tasks->where('project_id',$request->project);
    }
    
    // else {
    //   $tasks->whereNull('project_id');
    // }
    if($request->responsible)
    {
        if($request->responsible == -1){
            $tasks->WhereNull('responsible_id');
        }elseif($request->responsible == 'all') {
            $tasks->whereNotNull('responsible_id');
        }
        else {
            $tasks->where('responsible_id',$request->responsible)->whereNotNull('responsible_id');
        }
    }else {
      $tasks->orwhereNull('responsible_id');
    }

    if(!auth()->user()->hasRole('Full Administrator')){
        $tasks->where('responsible_id', auth()->user()->id);
    }


    $tasks = $tasks->with('responsible', 'project', 'task_status')->orderby('id','asc')->get();


    // if (! $tasks->toArray()) {
    //   return $this->sendResponse([], 'Tasks retrieved successfully.');
    // }






    $project = [];


        // foreach ($allStatus as $key => $status) {
    //   $arr['name'] = $status;
    //   $arr['tasks'] = $tasks->filter(function($tasks) use($key){
    //     return $tasks->status_id == $key + 1;
    //   })->toArray();
    //   $project['columns'][] = $arr;
    // }


    
    // generate all status
    $allStatus = ['open', 'pending', 'in-progress', 'done'];
    foreach ($allStatus as $status) {
      $arr['name'] = $status;
      $arr['tasks'] = [];
      $project['columns'][] = $arr;
    }


    foreach ($tasks->toArray() as $task) {
      $i=0;
      if (isset($project['columns'])) {
        foreach($project['columns'] as $status) {
          if ($status['name'] == $task['task_status']['name']) {
            $project['columns'][$i]['tasks'][] = $task;
          }
          $i++;
        }
      }
    }
    $project['columns'][3]['tasks'] = array_splice($project['columns'][3]['tasks'],0,10);
    return $this->sendResponse($project, 'Tasks retrieved successfully.');
  }


  public function allProjects() {
    $projects = Project::query();

    if(!auth()->user()->hasRole('Full Administrator')){
        $projects->whereHas('assigns',function($query){
            $query->where('assign_to', auth()->user()->id);
        });
    }

    $projects = $projects->pluck('name','id');

    return $this->sendResponse($projects, 'Projects retrieved successfully.');
  }

  public function tasksCard(CardTaskRequest $request)
  {
    $input = $request->validated();
    $tasks = Task::with('responsible', 'project', 'task_status')
                  ->where('project_id', $input['project_id'])
                  ->get();

    if (! $tasks->toArray()) {
      return $this->sendResponse([], 'Tasks retrieved successfully.');
    }

    $project = [];
    $project['name'] = $tasks->toArray()[0]['project']['name'];

    // generate all status
    $allStatus = ['open', 'pending', 'in-progress', 'done'];
    foreach ($allStatus as $status) {
      $arr['name'] = $status;
      $arr['tasks'] = [];
      $project['columns'][] = $arr;
    }

    foreach ($tasks->toArray() as $task) {
      $i=0;
      if (isset($project['columns'])) {
        foreach($project['columns'] as $status) {
          if ($status['name'] == $task['task_status']['name']) {
            $project['columns'][$i]['tasks'][] = $task;
          }
          $i++;
        }
      }
    }

    return $this->sendResponse($project, 'Tasks retrieved successfully.');
  }

  public function filterTasks(FilteTaskRequest $request)
  {
    $input = $request->validated();

    $tasks = Task::with('responsible', 'project')
              ->whereDate('start_at', '>=', $input['from_date'])
              ->whereDate('start_at', '<=', $input['to_date'])
              ->where('responsible_id', isset($input['employee_id']) ? $input['employee_id'] : auth()->user()->id)
              ->when($request->get('project_id'), function($query) use ($input) {
                $query->where('project_id', $input['project_id']);
              })->get();

    if (! $tasks)
      return $this->sendResponse([], 'Tasks retrieved successfully.');

    return $this->sendResponse(TaskResource::collection($tasks), 'Tasks retrieved successfully.');
  }

  // update task status
  public function update_task_status($id, $status_id)
  {
    $task = Task::find($id);

    if (!$task) {
      throw new ItemNotFoundException($id);
    }
    try {
      $updated = $task->fill(['status_id' =>  $status_id])->save();
    } catch (Exception $ex) {
      throw new ItemNotUpdatedException('Task');
    }

    if (!$updated)
      throw new ItemNotUpdatedException('Task');

    return $this->sendResponse(new TaskResource($task), 'Task updated successfully.');
  }


  public function tracked_tasks() {
    $tasks = Task::whereHas('tracking_history',function($track){
        $track->whereNull('end_at');
    })->paginate();
    return $this->sendResponse(new TaskCollection($tasks), 'Tracked tasks.');
  }

}
