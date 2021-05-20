<?php

namespace App\Http\Controllers\API;

use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemsNotFoundException;
use App\Http\Resources\User\UserCollection;
use App\models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ItemNotUpdatedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotDeletedException;
use App\Http\Resources\User\UserResource;
use Carbon\Carbon;
use App\Http\Requests\ClientRequest\AddClientRequest;
use App\Http\Requests\ClientRequest\ListClientRequest;

class ClientController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $clients = Client::paginate();
            return $this->sendResponse($clients, 'Clients retrieved successfully.');
        }catch(\Throwable $th){
            throw new ItemNotFoundException($th->getMessage());
        }

    }
        /**
     * get client paginated.
     *
     * @return \Illuminate\Http\Response
     */

    public function getClientsPaginated(ListClientRequest $request)
    {
        $input = $request->validated()['params'];

        if(!$request->filter){
            $clients = Client::all();
        }else {
            $filters = json_decode($request->filter);

            if($filters->value != null){
            $clients = Client::whereHas('attributes',function($q) use($filters){
                    $q->where('value','like','%'.json_encode($filters->value).'%');
                    if(isset($filters->id)){
                        $q->where('attribute_id',$filters->id);
                    }

            })->with(['roles','attributes']);
          }else {
            $clients = Client::all();
          }
        }

        $clients = $this->datatableOptions($clients, $input);
        $clients = $clients->latest()->paginate();

        return $this->sendResponse(new UserCollection($clients), 'clients retrieved successfully.');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddClientRequest $request)
    {
        $input = $request->validated();
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        $input['vacation'] = 0;
        $input['skip_vacation_limit'] = $request->skip_vacation_limit ? true : false;

        try {
            $client  = Client::create($input);

            if($request->time && $request->frequency && $request->first_run_time) {
                $client->client_time()->create([
                    'time'                => $request->time,
                    'frequency'           => $request->frequency,
                    'first_run_time'      => $request->first_run_time,
                    'last_run_time'       => $request->first_run_time,
                    'next_run_time'       => $this->next_run_time($request->first_run_time,$request->frequency),
                ]);
            }
        } catch (\Throwable $th) {
            throw new ItemNotCreatedException('Client');
        }

        $client->save();
        return $this->sendResponse(new UserResource($client), 'Client created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::find($id);
        return $this->sendResponse($client, 'Client retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $client = Clint::find($id);
        if (is_null($client)) {
            throw new ItemNotFoundException($id);
        }

        $input = $request->validated();
        $input = $request->all();
        $client->updated_at = Carbon::now();
        $client->updated_by = auth()->user()->id;

        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        }
        $input['skip_vacation_limit'] = $request->skip_vacation_limit ? true : false;
        $client = $client->fill($input);
        // save Client
        try {
            $client->save();

            if($request->time && $request->frequency && $request->first_run_time){

                $client->client_time()->updateOrCreate(['client_id' => $client->id],[
                    'time'                => $request->time,
                    'frequency'           => $request->frequency,
                    'first_run_time'      => $request->first_run_time,
                    'last_run_time'       => $request->first_run_time,
                    'next_run_time'       => $this->next_run_time($request->first_run_time,$request->frequency),
                ]);
            }
        } catch (Exception $th) {
            throw new ItemNotUpdatedException('Client not found');
        }

        return $this->sendResponse($client, 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // delete client
        $client = Client::find($id);
        if (is_null($client)) {
            throw new ItemNotFoundException($id);
        }

        try {
            $client->delete();
        } catch (\Throwable $th) {
            dd($th);
            throw new ItemNotDeletedException('Role');
        }

        return $this->sendResponse(new UserResource($client), 'client deleted successfully.');
    }
}
