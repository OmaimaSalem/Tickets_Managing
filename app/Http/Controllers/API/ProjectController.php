<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\ProjectRequest\AddProjectRequest;
use App\Http\Requests\ProjectRequest\CopyProjectRequest;
use App\Http\Requests\ProjectRequest\UpdateProjectRequest;
use App\Http\Requests\ProjectRequest\ViewProjectRequest;
use App\Http\Requests\ProjectRequest\DeleteProjectRequest;
use App\Http\Requests\ProjectRequest\ListProjectRequest;
use App\Http\Resources\Ticket\TicketCollection;
use App\Models\Project;
use App\Models\Status;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\API\BaseController;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotDeletedException;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectResource;
use Illuminate\Http\Request;

class ProjectController extends BaseController
{

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('permission:project-list|project-create|project-edit|project-delete|other-project-list|other-project-create|other-project-edit|other-project-delete', ['only' => ['index', 'list']]);
      $this->middleware('permission:project-create', ['only' => ['store']]);
      $this->middleware('permission:project-edit|other-project-edit', ['only' => ['update']]);
      $this->middleware('permission:project-delete|other-project-delete', ['only' => ['destroy']]);
      $this->middleware('permission:project-list|other-project-list', ['only' => ['getProjectsCountPerClient', 'getProjectPerClient','getAllByOwner']]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function index(ListProjectRequest $request)
  {
    $input = $request->validated()['params'];
    $request->merge(['index' => true]);
    if (auth()->user()->isAdmin() || auth()->user()->can('other-project-list')) {
      $projects = Project::with('owner');
    } else {
      $projectModel = new Project();
      $projects = $projectModel->ownProjects(auth()->user()->id);
    }

    // global search
    if (isset($input['global_search']) && $input['global_search']) {
      // to be all between ()
      $projects->where(function($query) use ($input){
        // in direct relation
        $query->orWhereHas('owner', function($q) use($input) {
          $q->where('name', 'like', '%'.$input['global_search'].'%');
        });
        // direct relation
        $query->orWhere('name','LIKE','%'.$input['global_search'].'%');
      });
    }

      if (isset($input['filters']) && $input['filters']) {
          foreach ($input['filters'] as $filterObj) {
              // first type of filter
              if ($filterObj['type'] == 'simple') {
                  if ($filterObj['name'] == 'number') {
                      $projects->where('id', 'like', '%'.$filterObj['text'].'%');
                  }
                  elseif ($filterObj['name'] == 'name') {
                      $projects->where('name', 'like', '%'.$filterObj['text'].'%');
                  }elseif ($filterObj['name'] == 'owner.name') {
                      $projects->whereHas('owner', function($query) use($filterObj) {
                          $query->where('name', 'like', '%'.$filterObj['text'].'%');
                      });
                  }
                  elseif ($filterObj['name'] == 'assigned.name') {
                      if($filterObj['text'] != 'All'){
                          if($filterObj['text'] == 'Not Assigned'){
                              $projects->has('assigns', '=', 0);
                          }
                          else{
                              $projects->whereHas('assigns', function($query) use($filterObj) {
                                  $query->where('name', 'like', '%' . $filterObj['text'] . '%') ;
                              });
                          }
                      }
                  }
                  else if ($filterObj['name'] == 'created_at') {
                      $projects->where('created_at', 'like', '%'.$filterObj['text'].'%');
                  }
                  // second type of filter
              } elseif ($filterObj['type'] == 'select') {
                  if ($filterObj['name'] == 'status.state' || $filterObj['name'] == 'status.name') {

                    if (auth()->user()->isAdmin() || auth()->user()->can('ticket-management')) {
                        //  $projects = Project::with('project.owner');
                        //  if($filterObj['selected_options'][0] == 'open') {
                        //     $projects->orWhere('status_id', '=', 1);
                        // } elseif($filterObj['selected_options'][0] == 'pending') {
                        //     $projects->orWhere('status_id', '=', 2);
                        // } elseif($filterObj['selected_options'][0] == 'in-progress') {
                        //     $projects->orWhere('status_id', '=', 3);
                        // } elseif($filterObj['selected_options'][0] == 'done') {
                        //     $projects->orWhere('status_id', '=', 4);
                        // } 
                        if( $filterObj['selected_options'][0] == 'open' || 
                            $filterObj['selected_options'][0] == 'pending' || 
                            $filterObj['selected_options'][0] == 'in-progress' || 
                            $filterObj['selected_options'][0] == 'done'){
                              $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                              if($status) {
                                $projects->where('projects.status_id', '=', $status->id);
                              }
                            }
                        elseif($filterObj['selected_options'][0] == 'favorites') {
                            $projects = auth()->user()->favorites();
                        }
                    } else {
                        //  $projectModel = new Project();
                        //  $projects = $projectModel->ownTickets(auth()->user()->id)->paginate();
                        //   if($filterObj['selected_options'][0] == 'open') {
                        //     $projects->where('status_id', '=', 1);
                        // } elseif($filterObj['selected_options'][0] == 'pending') {
                        //     $projects->orWhere('status_id', '=', 2);
                        // } elseif($filterObj['selected_options'][0] == 'in-progress') {
                        //     $projects->orWhere('status_id', '=', 3);
                        // } elseif($filterObj['selected_options'][0] == 'done') {
                        //     $projects->orWhere('status_id', '=', 4);
                        // } 
                        if( $filterObj['selected_options'][0] == 'open' || 
                            $filterObj['selected_options'][0] == 'pending' || 
                            $filterObj['selected_options'][0] == 'in-progress' || 
                            $filterObj['selected_options'][0] == 'done'){
                              $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                              if($status) {
                                $projects->where('projects.status_id', '=', $status->id);
                              }
                            }
                        elseif($filterObj['selected_options'][0] == 'favorites') {
                            $projects = auth()->user()->favorites();
                        }
                    }
                  }
              }
          }
      }
      //Sorting
      if (isset($input['sort']) && $input['sort']) {
          foreach ($input['sort'] as $sortObj) {
              //direct relation then in-direct relation
              if (in_array($sortObj['name'], ['created_at', 'name', 'id'])) {
                  if ($sortObj['order'] == 'desc') {
                  $projects->latest($sortObj['name']);
              } else {
                  $projects->oldest($sortObj['name']);
              }
            }
          }
        }



    $projects = $projects->latest()->paginate();

    return $this->sendResponse(new ProjectCollection($projects), 'Projects retrieved successfully.');
  }

  public function list(ListProjectRequest $request)
  {

    if($request->dropdown == "true"){
        $projects = Project::query();
        if($request->project && $request->project != ''){
            $projects->where('name','like','%'.$request->project.'%');
        }

        $projects = $projects->get(['name','id']);
        return $this->sendResponse(ProjectResource::collection($projects), 'Projects retrieved successfully.');
    }
    $projects = Project::all();
    return $this->sendResponse(ProjectResource::collection($projects), 'Projects retrieved successfully.');
  }


  /**
   * Display a data listing of the resource.
   *
   * @return Response
   */
  public function getAllByOwner(ListProjectRequest $request, $owner_id)
  {
    $owner_id = json_decode($owner_id);
    if(!is_array($owner_id)) $owner_id = [$owner_id];
    $projects = Project::with('tickets')->whereHas('owner', function ($query)  use ($owner_id) {
      $query->whereIN('owner_id', $owner_id);
    })->with('owner')->get();

    return $this->sendResponse(ProjectResource::collection($projects), 'Projects retrieved successfully.');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @return Response
   */
  public function store(AddProjectRequest $request)
  {
    $input = $request->validated();


    $input['created_at'] = Carbon::now();
    $input['created_by'] = auth()->user()->id;
    if($input['estimated_time'] == null) {
      $input['estimated_time'] = 0;
    }

    try {
      $project = Project::create($input);
    } catch (Exception $ex) {
        dd($ex);
      throw new ItemNotCreatedException('Project');
    }

    return $this->sendResponse(new ProjectResource($project->load('owner')), 'Project created successfully.');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function show(Project $project, ViewProjectRequest $request)
  {
    return $this->sendResponse(new ProjectResource($project), 'Project retrieved successfully.');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update(Project $project, UpdateProjectRequest $request)
  {
    if(!can_permission('edit','project',$project->created_by)){
        throw new ItemNotUpdatedException('Project');
    }

    $input = $request->validated();
    if($input['estimated_time'] == null) {
      $input['estimated_time'] = 0;
    }

    $project->updated_at = Carbon::now();
    $project->updated_by = auth()->user()->id;
    try {
      $updated = $project->fill($input)->save();
    } catch (\Exception $ex) {
        dd($ex);
      throw new ItemNotUpdatedException('Project');
    }

    if (!$updated)
      throw new ItemNotUpdatedException('Project');

    return $this->sendResponse(new ProjectResource($project), 'Project updated successfully.');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy(Project $project, DeleteProjectRequest $request)
  {

    if(!can_permission('delete','project',$project->created_by)){
        throw new ItemNotDeletedException('Project');
    }


    if($project->tickets && $project->tickets->isNotEmpty()) {
      throw new InvalidDataException([
        'tickets' => $project->tickets->toArray()
      ],
      'Can\'t delete!, Project has ticket.');
    }

    if($project->tasks && $project->tasks->isNotEmpty()) {
      throw new InvalidDataException([
        'tasks' => $project->tasks->toArray()
      ],
      'Can\'t delete!, Project has tasks.');
    }

    try {
      $project->delete();
    } catch (\Exception $ex) {
      dd($ex);
      throw new ItemNotDeletedException('Project');
    }

    return $this->sendResponse(new ProjectResource($project), 'Project deleted successfully.');
  }


  /**
   * Search in projects.
   *
   * @param  int  $searchKey
   * @return Response
   */
  public function search($searchKey)
  {
    $project_model = new Project();
    $projects = $project_model->search($searchKey);

    return $this->sendResponse($projects->toArray(), 'Projects retrieved successfully.');
  }

  public function getProjectsCountPerClient($id)
  {
     $user = User::find($id);
    if($user->type == 'client'){
      $projectsNumber = Project::whereHas('owner', function ($query)  use ($id) {
          $query->where('owner_id','=', $id);
      })->count();
    }
    else {
      $projectsNumber = Project::whereHas('assigns', function ($query)  use ($id) {
        $query->where('assign_to','=', $id);
    })->count();
    }


    return $this->sendResponse(['projectsNumber' => $projectsNumber], 'Projects Number retrieved successfully.');
  }

  public function getProjectsPerClient($id)
  {
    $user = User::find($id);
    if($user->type == 'client'){
      $projects = Project::whereHas('owner', function ($query)  use ($id) {
          $query->where('owner_id','=', $id);
      })->paginate();
    }
    else{
      $projects = Project::whereHas('assigns', function ($query)  use ($id) {
        $query->where('assign_to','=', $id);
    })->paginate();
    }

    return $this->sendResponse(new ProjectCollection($projects), 'Projects retrieved successfully.');
  }


  public function mergeProjects(Request $request) {

    // get merged project
    $project      = Project::findOrFail($request->project);

    // get parent project to merged in
    $into_project = Project::findOrFail($request->into_project);

    // move tasks and projects into parent project
    $project->tasks()->update(['project_id' => $into_project->id]);
    $project->tickets()->update(['project_id' => $into_project->id]);

    // merge to projects assigns and owners
    $assigns = $project->assigns->merge($into_project->assigns)->unique();
    $owners  = $project->owner->merge($into_project->owner)->unique();

    // remove parent old assigns and owners
    $into_project->assigns()->detach();
    $into_project->owner()->detach();

    // add assigns and owners to parent project
    $into_project->assigns()->attach($assigns);
    $into_project->owner()->attach($owners);


    // delete old project
    $project->delete();

    return $this->sendResponse($project->id, 'Projects retrieved successfully.');

  }

  public function addToFav($project_id){

      $project = Project::find($project_id);

      //$project->favoriteAdders()->attach(auth()->user()->id);

      $project->favoriteAdders()->attach(auth()->user()->id);

      return $this->sendResponse(true, 'Project Added to favorite list successfully.');

  }

    public function RemoveFromFav($project_id){

        $project = Project::find($project_id);

        $project->favoriteAdders()->detach(auth()->user()->id);

        return $this->sendResponse(true, 'Project removed from favorite list successfully.');

    }

    public function getFavorites(){


      $projects = auth()->user()->favorites()->get();

      return $this->sendResponse(ProjectResource::collection($projects),'Favorite projects retrieved successfully');

    }

    public function copyProject(CopyProjectRequest $request){
        $selectedTicketsIDs = $request->selectedTickets;
        $selectedTasksIDs = $request->selectedTasks;
        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;

        $input['owner'] = $request->owners;
        $input['project_assign'] = $request->assigns;
        $request->merge(['owner' => $request->owners,'project_assign' => $request->assigns]);
        unset($input['owners']);
        unset($input['assigns']);
        try {
            $project = Project::create($input);
        } catch (Exception $ex) {
            throw new ItemNotCreatedException('Project');
        }
        $Tickets = Ticket::find($selectedTicketsIDs);
        foreach ($Tickets as $ticket){
            $newTicket = $ticket->replicate();
            $newTicket->project_id = $project->id;
            $newTicket->save();
        }
        $Tasks = Task::find($selectedTasksIDs);
        foreach ($Tasks as $task){
            $newTasks = $task->replicate();
            $newTasks->project_id = $project->id;
            $newTasks->save();
        }
        return $this->sendResponse(new ProjectResource($project->load('owner')), 'Project Copied successfully.');
    }

    public function getCalender($project_id){
      $project = Project::find($project_id);
      $tickets = $project->tickets;
      $ticketsData = [];
      foreach ($tickets as $ticket){
        array_push($ticketsData, $ticket->only(['id','name','created_at', 'due_date', 'status_id']));
      }
      $tasks = $project->tasks;
      $tasksData = [];
      foreach ($tasks as $task){
        array_push($tasksData, $task->only(['id','name','start_at', 'deadline', 'status_id']));
      }
      return response()->json([$ticketsData , $tasksData]);
  }

}
