<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\UserRequest\AddUserRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Http\Requests\UserRequest\ViewUserRequest;
use App\Http\Requests\UserRequest\DeleteUserRequest;
use App\Http\Requests\UserRequest\ListUserRequest;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Models\Metadata;
use App\Models\Project;
use http\Env\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotDeletedException;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use Carbon\Carbon;


use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;




class UsersController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index', 'list']]);
        $this->middleware('permission:user-create', ['only' => ['store']]);
        $this->middleware('permission:user-edit', ['only' => ['update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
        $this->middleware('permission:user-list|ticket-management', ['only' => ['getClients','getAllResponsibles']]);

    }

    /**
     * Retrive all users paginated.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ListUserRequest $request)
    {
        $users = User::with('roles')->paginate();
        return $this->sendResponse(new UserCollection($users), 'users retrieved successfully.');
    }

    /**
     * Retrive all clients paginated.
     *
     * @return \Illuminate\Http\Response
     */
    public function getClientsPaginated(ListUserRequest $request)
    {
        $input = $request->validated()['params'];

        if(!$request->filter){
            $users = User::where('type', 'client')->with(['roles','attributes']);
        }else {
            $filters = json_decode($request->filter);

            if($filters->value != null){
            $users = User::where('type', 'client')->whereHas('attributes',function($q) use($filters){
                    $q->where('value','like','%'.json_encode($filters->value).'%');
                    if(isset($filters->id)){
                        $q->where('attribute_id',$filters->id);
                    }

            })->with(['roles','attributes']);
          }else {
            $users = User::where('type', 'client')->with(['roles','attributes','client_time']);
          }
        }

        $users = $this->datatableOptions($users, $input);
        $users = $users->with('metadata')->latest()->paginate();

        try{
            return $this->sendResponse($users, 'clients retrieved successfully.');

        }catch(\Exception $e){
            return $e->getMessage;
        }
    }

    /**
     * Retrive all employees paginated.
     *
     * @return \Illuminate\Http\Response
     */
    public function getEmployeesPaginated(ListUserRequest $request)
    {
        $input = $request->validated()['params'];

        if(!$request->filter){
            $users = User::where('type', 'regular-user')->with(['roles','attributes']);
        }else {
            $filters = json_decode($request->filter);

            if($filters->value != null){
            $users = User::where('type', 'regular-user')->whereHas('attributes',function($q) use($filters){
                    $q->where('value','like','%'.json_encode($filters->value).'%');
                    if(isset($filters->id)){
                        $q->where('attribute_id',$filters->id);
                    }

            })->with(['roles','attributes']);
          }else {
            $users = User::where('type', 'regular-user')->with(['roles','attributes']);
          }
        }

        //datatableOptions For globalSearc & MultiSearch & Sorting
        $users = $this->datatableOptions($users, $input);
        $users = $users->latest()->paginate();

        if($request->export_excel){
            return Excel::download(new UsersExport($users), date('d-m-Y').'-users.xlsx');
        }

        return $this->sendResponse(new UserCollection($users), 'employees retrieved successfully.');
    }

    /**
     * Display a data listing of the resource.
     *
     * @return Response
     */
    public function getClients(ListUserRequest $request)
    {

        $clients = User::where('type', 'client');
          if($request->dropdown && $request->dropdown== "true"){
                $clients = $clients->get(['id','name']);
          }  else {
                $clients = $clients->get();
          }

        return $this->sendResponse(UserResource::collection($clients), 'Clients retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddUserRequest $request)
    {
       // return $request->all();
        $input = $request->validated();
        $input = $request->all();

        $input['password'] = Hash::make($input['password']);
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        $input['vacation'] = 0;
        $input['skip_vacation_limit'] = $request->skip_vacation_limit ? true : false;   
        // $user  = User::create($input);

        try {
            $user  = User::create($input);

            if($user->type == "client" && $request->time && $request->frequency && $request->first_run_time) {
                $user->client_time()->create([
                    'time'                => $request->time,
                    'frequency'           => $request->frequency,
                    'first_run_time'      => $request->first_run_time,
                    'last_run_time'       => $request->first_run_time,
                    'next_run_time'       => $this->next_run_time($request->first_run_time,$request->frequency),
                ]);
            }
            if($user){
                $user->metadata()->create($input);
            }
        } catch (\Throwable $th) {
            dd($th);
            throw new ItemNotCreatedException('User');
        }
        // $user->save();
        return $this->sendResponse(new UserResource($user), 'users created successfully.');
    }

    public function addMetadataToUser($user_id, $input){
        $input['user_id'] = $user_id;
        $input['updated_by'] = auth()->user()->id;
        $meatadata = Metadata::create($input);
        return $input ;
    }

    public function next_run_time($time,$frequency) {
        $next_time = "";
        switch($frequency){
            case "weekly":
                $next_time = Carbon::parse($time)->addWeeks(1);
            break;
            case "monthly":
                $next_time = Carbon::parse($time)->addMonths(1);
            break;

            case "yearly":
                $next_time = Carbon::parse($time)->addYears(1);
            break;

            default:
                $next_time = Carbon::parse($time)->addMonths(1);
            break;
        }
        return $next_time;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(User $user, ViewUserRequest $request)
    {
        $request->merge(['roles' => true]);
        $user = $user->load(['roles.permissions','metadata']);
        if($user->metadata && $user->metadata->timezone){
            date_default_timezone_set($user->metadata->timezone);
        }
        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        // return $this->sendResponse($request->all(), 'users data successfully.');

        $user = User::find($id);
        if (is_null($user)) {
            throw new ItemNotFoundException($id);
        }

        $input = $request->validated();
        $input = $request->all();
        $user->updated_at = Carbon::now();
        $user->updated_by = auth()->user()->id;


        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }
        $input['skip_vacation_limit'] = $request->skip_vacation_limit ? true : false;

        $user = $user->fill($input);

        // save User
        try {
            $metadata = MetaData::where('user_id', $user->id)->first();
            
            if($metadata)
                $metadata->update($input);
            else
            $user->metadata()->create($input);

            $user->save();
            if($user->type == "client" && $request->time && $request->frequency && $request->first_run_time){

                $user->client_time()->updateOrCreate(['client_id' => $user->id],[
                    'time'                => $request->time,
                    'frequency'           => $request->frequency,
                    'first_run_time'      => $request->first_run_time,
                    'last_run_time'       => $request->first_run_time,
                    'next_run_time'       => $this->next_run_time($request->first_run_time,$request->frequency),
                ]);
            }
            
        } catch (\Exception $th) {
             throw new ItemNotUpdatedException('User');
        }

        return $this->sendResponse(new UserResource($user), 'users updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUserRequest $request, $id)
    {
        // delete user
        $user = User::with('metadata')->find($id);

        if (is_null($user)) {
            throw new ItemNotFoundException($id);
        }
        $userMetadata =  Metadata::where('user_id', $id)->first();

        try {
            $user->metadata()->delete();
            $user->delete();
        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw new ItemNotDeletedException('Role');
        }

        return $this->sendResponse(new UserResource($user), 'users deleted successfully.');
    }

    public function getAllResponsibles(ListUserRequest $request)
    {
        $users = User::where('type', 'regular-user');
          if($request->dropdown && $request->dropdown=="true"){
                $users = $users->get(['id','name']);
          }  else {
                $users = $users->get();
          }
        return $this->sendResponse(UserResource::collection($users), 'Users retrieved successfully.');
    }

    private function dataTableOptions($users, $input)
    {
        // global search
        if (isset($input['global_search']) && $input['global_search']) {
            // to be all between ()
            $users->where(function($query) use ($input){
            // in direct relation
            $query->whereHas('roles', function($query) use($input) {
                $query->where('name', 'like', '%'.$input['global_search'].'%');
            });
            // direct relation
            $query->orWhere('users.name','LIKE','%'.$input['global_search'].'%');
            $query->orWhere('users.email','LIKE','%'.$input['global_search'].'%');
            $query->orWhere('users.type','LIKE','%'.$input['global_search'].'%');
            $query->orWhere('users.created_at','LIKE','%'.$input['global_search'].'%');
            });
        }

        // sorting
        if (isset($input['sort']) && $input['sort']) {
            foreach ($input['sort'] as $sortObj) {
            //direct relation then in-direct relation
            if (in_array($sortObj['name'], ['created_at', 'name', 'type', 'email'])) {
                if ($sortObj['order'] == 'desc') {
                $users->latest($sortObj['name']);
                } else {
                $users->oldest($sortObj['name']);
                }
                // indirect relation
            } elseif ($sortObj['name'] == 'roles.name') {
                $users->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id');
                $users->join('roles', 'model_has_roles.role_id', '=', 'roles.id');
                $users->orderBy('roles.name', $sortObj['order']);
            }
            }
        }

        // filter
        if (isset($input['filters']) && $input['filters']) {
            foreach ($input['filters'] as $filterObj) {
                // first type of filter
                if ($filterObj['type'] == 'simple') {
                    if ($filterObj['name'] == 'name') {
                        $users->where('name', 'like', '%'.$filterObj['text'].'%');
                    } elseif ($filterObj['name'] == 'email') {
                        $users->where('email', 'like', '%'.$filterObj['text'].'%');
                    } elseif ($filterObj['name'] == 'role') {
                        $users->whereHas('roles', function ($query) use ($filterObj) {
                            $query->where('name', 'like', '%'.$filterObj['text'].'%');
                        });
                    } elseif ($filterObj['name'] == 'lang') {
                        if($filterObj['text'] != 'all'){
                            $users->whereHas('metadata', function ($query) use ($filterObj) {
                                $query->where('language', 'like', '%'.$filterObj['text'].'%');
                            });
                        }
                    } elseif ($filterObj['name'] == 'created_at') {
                        $users->where('created_at', 'like', '%' . $filterObj['text'] . '%');
                    }
                }
            }
        }

        return $users;
    }

    public function addToSpam($id) {
        $user = User::find($id);
        if (!$user) {
            throw new ItemNotFoundException($id);
        }
        $user->spam = 1;
        $user->save();
        return $this->sendResponse($user, 'user added to spam list.');

    }

    public function removeFromSpam($id) {
        $user = User::find($id);
        if (!$user) {
            throw new ItemNotFoundException($id);
        }
        $user->spam = 0;
        $user->save();
        return $this->sendResponse($user, 'user removed from spam list.');
    }

    public function addToSupport($id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new ItemNotFoundException($id);
        }
        $user->support = 1;
        $user->save();
        return $this->sendResponse($user, 'user added to support list.');
    }

    public function removeFromSupport($id)
    {
        $user = User::find($id);
        if (!$user) {
            throw new ItemNotFoundException($id);
        }
        $user->support = 0;
        $user->save();
        return $this->sendResponse($user, 'user removed from support list.');
    }

    //Getting all clients related to a single project
    public function getClientsPerProject($id)
    {
      $clients = Project::find($id)->owner()->get();
      return $this->sendResponse(UserResource::collection($clients), 'Clients retrieved successfully.');
    }

    public function get_current_user_working_time(){
        $time = auth()->user()->get_all_time();
        return $this->sendResponse($time, 'current user working time retrived successfully.');
    }

    
}
