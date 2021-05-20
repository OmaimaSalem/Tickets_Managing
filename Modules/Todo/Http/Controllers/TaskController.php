<?php

namespace Modules\Todo\Http\Controllers;


use  App\Models\User;
use  Modules\Todo\Entities\Task;
use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Todo\Http\Requests\CreateTaskRequest;
use Modules\Todo\Http\Requests\UpdateTaskStatus;
use Modules\Todo\Http\Requests\UpdateTaskCard;
use Modules\Todo\Http\Resources\TaskCollection;
use Modules\Todo\Http\Resources\TaskResource;

class TaskController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
      $tasks = Task::orderBy('created_at', 'DESC')->paginate();
      return $this->sendResponse(new TaskCollection($tasks), 'Tasks retrieved successfully.');
      // return $this->sendResponse(['users', User::all()],new TaskCollection($tasks))
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('todo::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
     public function store(CreateTaskRequest $request)
     {
       $input = $request->validated();
       $input['created_at'] = Carbon::now();
       $input['status'] = false;
       $input['user_id'] = 0;
       $input['last_time_work'] = date('Y-m-d H:i:s');
       $input['next_time_work'] = $this->next_run_date($input['freq']);
       //$input['created_by'] = auth()->user()->id;
       $task = Task::create($input);

       //adding task's assigned users to the task_user table
       if($request->assigned_to){
           $task->users()->attach($request->assigned_to);
       }

       return $this->sendResponse(new TaskResource($task), 'Task created successfully.');
     }




     public function next_run_date($type) {
      $carbon = Carbon::parse(date('Y-m-d H:i:s'));
      $date   = date('Y-m-d H:i:s');
      switch($type) {
          case "daily":
               $date = $carbon->addDay();
          break;
          case "weekly":
               $date = $carbon->addWeek();
          break;

          case "monthly":
               $date = $carbon->addMonth();
          break;

          case "yearly":
               $date = $carbon->addYear();
          break;

          break;
          default:
           $date = date('Y-m-d H:i:s');
      }
      return $date;
  }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
      $task = Task::find($id);
      return $this->sendResponse(new TaskResource($task), 'Task retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('todo::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
     //Task $task, UpdateTaskRequest $request
     public function update(Request $request)
     {
        //dd($request->id);
       $task = Task::find($request->id);
       $task->name = $request->name;
       $task->description = $request->description;
       $task->deadline = $request->deadline;
       $task->save();

       //updating task's assigned users in the task_user table
       if($request->assigned_to){
           $task->users()->sync($request->assigned_to);
       }

       return $this->sendResponse(new TaskResource($task), 'Task updated successfully.');
     }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
      $task = Task::find($id);

      $task->delete();

      //deleting task's assigned users from the task_user table      
      if($task->assigned_to){
           $task->detach($task->users);
       }

      return $this->sendResponse(new TaskResource($task), 'Task deleted successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function updateTaskStatus(UpdateTaskStatus $request)
    {
      $task = Task::find($request->id);
      $task->status = $request->status;
      $task->save();
      return $this->sendResponse(new TaskResource($task), 'Task Status updated successfully.');
    }

    public function updateTaskCard(UpdateTaskCard $request)
    {
      $task = Task::find($request->id);
      $task->card_id = $request->card_id;
      $task->save();
      return $this->sendResponse(new TaskResource($task), 'Task Transfered successfully.');
    }
    public function sortTasks(Request $request)
    {
      //dd($request->all());
      $task1 = Task::find($request->id1);
      $task1->id = 0;
      $task1->save();
      $task2 = Task::find($request->id2);
      $task2->id = $request->id1;
      $task2->save();
      $task1 = Task::find($task1->id);
      $task1->id = $request->id2;
      $task1->save();
      return $this->sendResponse( "", 'Tasks sorted successfully.');
    }
}
