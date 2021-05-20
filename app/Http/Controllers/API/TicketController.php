<?php

namespace App\Http\Controllers\API;

use App\Exceptions\InvalidDataException;
use App\Exceptions\ItemNotCreatedException;
use App\Exceptions\ItemNotDeletedException;
use App\Exceptions\ItemNotFoundException;
use App\Exceptions\ItemNotUpdatedException;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\DeleteMultiTicketRequest;
use App\Http\Requests\TicketRequest\AddTicketRequest;
use App\Http\Requests\TicketRequest\DeleteTicketRequest;
use App\Http\Requests\TicketRequest\ListTicketRequest;
use App\Http\Requests\TicketRequest\UpdateTicketRequest;
use App\Http\Requests\TicketRequest\ViewTicketRequest;
use App\Http\Requests\TicketRequestDeleteMultiTicketRequest;
use App\Http\Resources\Ticket\TicketCollection;
use App\Http\Resources\Ticket\TicketResource;
use App\Jobs\Ticket\TicketAssignJob;
use App\Models\Project;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketCollection as Ticket_Collection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\TicketConversation\Entities\TicketConversation;
use Validator;
use \App\Models\TrackTicket;
use Illuminate\Support\Facades\Storage;


use App\Notifications\Admin\Ticket\AssignTicketNotification;
use App\Notifications\Admin\Ticket\ArchiveTicketNotification;

class TicketController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('permission:ticket-list|ticket-create|ticket-edit|ticket-delete|other-ticket-list|other-ticket-create|other-ticket-edit|other-ticket-delete', ['only' => ['index', 'show', 'getTicketsByProjectId']]);
        $this->middleware('permission:ticket-create', ['only' => ['store']]);
        $this->middleware('permission:ticket-edit|other-ticket-edit', ['only' => ['update']]);
        $this->middleware('permission:ticket-delete|other-ticket-delete', ['only' => ['destroy']]);
        $this->middleware('permission:ticket-list|other-ticket-list', ['only' => ['getTicketsCountPerClient', 'getTicketsPerClient']]);
    }

    /**
     * Display a data listing of the resource.
     *
     * @return Response
     */
    public function index(ListTicketRequest $request)
    {
        $input = $request->validated()['params'];
        if (auth()->user()->isAdmin() || auth()->user()->can('other-ticket-list')) {
            $tickets = Ticket::with('project.owner');
        } else {
            $ticketModel = new Ticket();
            $tickets = $ticketModel->ownTickets(auth()->user()->id);
        }

        // global search
        if (isset($input['global_search']) && $input['global_search']) {
            $input['filters'] = [];
            $global_search = trim($input['global_search']);
            $tickets->where(function ($query) use ($input, $global_search) {
                $query->whereHas('ticket_status', function ($q) use ($input, $global_search) {
                    $q->where('name', 'like', '%' . $global_search . '%');
                });
                $query->orWhereHas('project', function ($q) use ($input, $global_search) {
                    $q->where('name', 'like', '%' . $global_search . '%');
                });
                $query->orWhereHas('project.owner', function ($q) use ($input, $global_search) {
                    $q->where('name', 'like', '%' . $global_search . '%');
                });
                $query->orWhereHas('project.owner', function ($q) use ($input, $global_search) {
                    $q->where('email', 'like', '%' . $global_search . '%');
                });
                $query->orWhere('tickets.name', 'LIKE', '%' . $global_search . '%');
                $query->orWhere('tickets.number', 'LIKE', '%' . $global_search . '%');
                $query->orWhere('tickets.created_at', 'LIKE', '%' . $global_search . '%');
            });
        }
        $getArchive = false;
        $getDone = false;
        // filter
        if (isset($input['filters']) && $input['filters']) {
            foreach ($input['filters'] as $filterObj) {
                // first type of filter
                if ($filterObj['type'] == 'simple') {
                    if ($filterObj['name'] == 'number') {
                        $tickets->where('number', 'like', '%' . trim($filterObj['text']) . '%');
                    } else if ($filterObj['name'] == 'name') {
                        $tickets->where('name', 'like', '%' . trim($filterObj['text']) . '%');
                    } elseif ($filterObj['name'] == 'owner.name') {
                        $tickets->whereHas('owner', function ($query) use ($filterObj) {
                            $query->where('name', 'like', '%' . trim($filterObj['text']) . '%');
                        });
                    } elseif ($filterObj['name'] == 'owner.email') {
                        $tickets->whereHas('owner', function ($query) use ($filterObj) {
                            $query->where('email', 'like', '%' . trim($filterObj['text']) . '%');
                        });
                    } elseif ($filterObj['name'] == 'project.name') {
                        $tickets->whereHas('project', function ($query) use ($filterObj) {
                            $query->where('name', 'like', '%' . trim($filterObj['text']) . '%');
                        });
                    } elseif ($filterObj['name'] == 'created_at') {
                        $tickets->where('created_at', 'like', '%' . trim($filterObj['text']) . '%');
                    } elseif ($filterObj['name'] == 'assigned_to') {
                        if ($filterObj['text'] != 'All') {
                            if ($filterObj['text'] == 'Not Assigned') {
                                $tickets->has('manager', '=', 0);
                            } else {
                                $tickets->whereHas('manager', function ($query) use ($filterObj) {
                                    $query->where('name', 'like', '%' . trim($filterObj['text']) . '%');
                                });
                            }
                        }
                    }
                } elseif ($filterObj['type'] == 'search') {
                    if ($filterObj['selected_options'][0] == 'todayTickets') {
                        $tickets->whereDate('created_at', Carbon::today());
                        $tickets->OrWhereDate('updated_at', Carbon::today());
                    } elseif ($filterObj['selected_options'][0] == 'myTickets') {
                        $tickets->whereHas('manager', function ($query) use ($filterObj) {
                            $query->where('manager_id', '=', auth()->user()->id);
                        });
                    } elseif ($filterObj['selected_options'][0] == 'yesterdayTickets') {
                        $tickets->whereDate('created_at', Carbon::yesterday());
                        $tickets->orWhereDate('updated_at', Carbon::yesterday());
                    }
                } elseif ($filterObj['type'] == 'select') {
                    if ($filterObj['name'] == 'status.name') {
                        $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                        if ($status) {
                            $tickets->where('tickets.status_id', '=', $status->id);
                        }

                    } elseif ($filterObj['name'] == 'status.state') {
                        if (auth()->user()->isAdmin() || auth()->user()->can('ticket-management')) {
                            if ($filterObj['selected_options'][0] == 'read') {
                                $tickets->where('read', '=', 1);
                            } elseif ($filterObj['selected_options'][0] == 'unread') {
                                $tickets->orWhere('read', '=', 0);
                            } elseif ($filterObj['selected_options'][0] == 'reply') {
                                $tickets->orWhere('reply', '=', 1);
                            } elseif ($filterObj['selected_options'][0] == 'unreplied') {
                                $tickets->orWhere('reply', '=', 0);
                            } elseif ($filterObj['selected_options'][0] == 'due_today') {
                                $tickets->orWhereDate('due_date', '=', Carbon::today());
                            }
                            // elseif($filterObj['selected_options'][0] == 'open') {
                            //     $tickets->orWhere('status_id', '=', 1);
                            // } elseif($filterObj['selected_options'][0] == 'pending') {
                            //     $tickets->orWhere('status_id', '=', 2);
                            // } elseif($filterObj['selected_options'][0] == 'in-progress') {
                            //     $tickets->orWhere('status_id', '=', 3);
                            // } elseif($filterObj['selected_options'][0] == 'done') {
                            //     $getDone = true;
                            //     $tickets->orWhere('status_id', '=', 4);
                            // }
                            elseif ($filterObj['selected_options'][0] == 'unproject') {
                                $tickets->orWhere('project_id', '=', null);
                            } elseif ($filterObj['selected_options'][0] == 'withTask') {
                                $tickets->orWhereHas('tasks');
                            } elseif ($filterObj['selected_options'][0] == 'withoutTask') {
                                $tickets->orWhereDoesntHave('tasks');
                            } elseif ($filterObj['selected_options'][0] == 'todayTickets') {
                                $tickets->where('created_at', Carbon::today());
                                $tickets->orWhere('updated_at', Carbon::today());
                            } elseif ($filterObj['selected_options'][0] == 'archive') {
                                $getArchive = true;
                                $tickets->orWhere('archive', '=', 1);
                            } elseif ($filterObj['selected_options'][0] == 'open' ||
                                $filterObj['selected_options'][0] == 'pending' ||
                                $filterObj['selected_options'][0] == 'in-progress' ||
                                $filterObj['selected_options'][0] == 'done') {
                                $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                                if ($status) {
                                    $tickets->where('tickets.status_id', '=', $status->id);
                                }
                                if ($status->id == 4) {
                                    $getDone = true;
                                }
                            }
                        } else {
//              $ticketModel = new Ticket();
//              $tickets = $ticketModel->ownTickets(auth()->user()->id)->paginate();
                            if ($filterObj['selected_options'][0] == 'read') {
                                $tickets->where('read', '=', 1);
                            } elseif ($filterObj['selected_options'][0] == 'unread') {
                                $tickets->orWhere('read', '=', 0);
                            } elseif ($filterObj['selected_options'][0] == 'reply') {
                                $tickets->orWhere('reply', '=', 1);
                            } elseif ($filterObj['selected_options'][0] == 'unreplied') {
                                $tickets->orWhere('reply', '=', 0);
                            } elseif ($filterObj['selected_options'][0] == 'due_today') {
                                $tickets->orWhereDate('due_date', '=', Carbon::today());
                            }
                            // elseif($filterObj['selected_options'][0] == 'open') {
                            //     $tickets->orWhere('status_id', '=', 1);
                            // } elseif($filterObj['selected_options'][0] == 'pending') {
                            //     $tickets->orWhere('status_id', '=', 2);
                            // } elseif($filterObj['selected_options'][0] == 'in-progress') {
                            //     $tickets->orWhere('status_id', '=', 3);
                            // } elseif($filterObj['selected_options'][0] == 'done') {
                            //     $getDone = true;
                            //     $tickets->orWhere('status_id', '=', 4);
                            // }
                            elseif ($filterObj['selected_options'][0] == 'unproject') {
                                $tickets->orWhere('project_id', '=', null);
                            } elseif ($filterObj['selected_options'][0] == 'withTask') {
                                $tickets->orWhereHas('tasks');
                            } elseif ($filterObj['selected_options'][0] == 'withoutTask') {
                                $tickets->orWhereDoesntHave('tasks');
                            } elseif ($filterObj['selected_options'][0] == 'myTickets') {
                                $tickets->orWhereHas('manager', function ($query) use ($filterObj) {
                                    $query->where('manager_id', '=', auth()->user()->id);
                                });
                            } elseif ($filterObj['selected_options'][0] == 'archive') {
                                $getArchive = true;
                                $tickets->orWhere('archive', '=', 1);
                            } elseif ($filterObj['selected_options'][0] == 'open' ||
                                $filterObj['selected_options'][0] == 'pending' ||
                                $filterObj['selected_options'][0] == 'in-progress' ||
                                $filterObj['selected_options'][0] == 'done') {
                                $status = Status::where('name', '=', $filterObj['selected_options'][0])->first();
                                if ($status) {
                                    $tickets->where('tickets.status_id', '=', $status->id);
                                }
                                if ($status->id == 4) {
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
                if (in_array($sortObj['name'], ['due_date', 'created_at', 'name', 'number', 'collection'])) {
                    if ($sortObj['order'] == 'desc') {
                        $tickets->latest($sortObj['name']);
                    } else {
                        $tickets->oldest($sortObj['name']);
                    }
                }
            }
        }
        if ($getDone == false) {
            $tickets->where('status_id', '!=', 4);
        }
        $tickets->where('spam', '=', null);
        if ($getArchive == false) {
            $tickets->where('archive', '=', null);
        }
        $tickets = $tickets->latest('updated_at')->paginate(30);
        return $this->sendResponse(new TicketCollection($tickets), 'Tickets retrieved successfully.');
    }

    private function getTicketStatusCount($status)
    {
        $mystatus = Status::where('name', '=', $status)->first();
        if ($mystatus) {
            $status = Ticket::where('status_id', '=', $mystatus->id)->count();
        }
        return $status;
    }

    public function getTicketFiltered(Request $request)
    {
        $data2 = [];
        $data3 = [];
        for ($i = 6; $i >= 0; $i--) {
            $ticket = Ticket::whereDate('created_at', '=', Carbon::today()->subDay($i)->toDateString())->count();
            $today = Carbon::today()->subDay($i)->format('D');
            array_push($data2, $ticket);
            array_push($data3, $today);
        }

        $tickets = Ticket::select(['id', 'read', 'reply', 'due_date', 'status_id'])->get();

        $all = $tickets->count();
        $read = $tickets->filter(function ($ticket) {
            return $ticket->read == 1;
        })->count();

        $unread = $tickets->filter(function ($ticket) {
            return $ticket->read == 0;
        })->count();

        $unread = $tickets->filter(function ($ticket) {
            return $ticket->read == 0;
        })->count();

        $reply = $tickets->filter(function ($ticket) {
            return $ticket->reply == 1;
        })->count();


        $unreplied = $tickets->filter(function ($ticket) {
            return $ticket->reply == 0;
        })->count();


        $due_today = $tickets->filter(function ($ticket) {
            return $ticket->due_date == Carbon::now('Africa/Cairo');
        })->count();


        // $all = Ticket::all()->count();
        // $read = Ticket::where('read', '=', 1)->count();
        // $unread = Ticket::where('read', '=', 0)->count();
        // $reply = Ticket::where('reply', '=', 1)->count();
        // $unreplied = Ticket::where('reply', '=', 0)->count();
        // $due_today = Ticket::where('due_date', '=', Carbon::now('Africa/Cairo'))->count();
        // $open = $this->getTicketStatusCount('open');
        // $pending = $this->getTicketStatusCount('pending');
        // $in_progress = $this->getTicketStatusCount('in-progress');
        // $done = $this->getTicketStatusCount('done');

        $open = $tickets->filter(function ($ticket) {
            return $ticket->status_id == 1;
        })->count();

        $pending = $tickets->filter(function ($ticket) {
            return $ticket->status_id == 2;
        })->count();

        $in_progress = $tickets->filter(function ($ticket) {
            return $ticket->status_id == 3;
        })->count();

        $done = $tickets->filter(function ($ticket) {
            return $ticket->status_id == 4;
        })->count();


        $data = [
            'all' => $all,
            'open' => $open,
            'pending' => $pending,
            'inProgress' => $in_progress,
            'done' => $done,
            'read' => $read,
            'unread' => $unread,
            'reply' => $reply,
            'unreplied' => $unreplied,
            'due_today' => $due_today
        ];
        return response()->json(['ticketStatus' => $data, 'weekData' => $data2, 'days' => $data3]);
    }

    public function getTicketChartsData(Request $request)
    {
        // $open = $this->getTicketStatusCount('open');
        // $pending = $this->getTicketStatusCount('pending');
        // $in_progress = $this->getTicketStatusCount('in-progress');
        // $done = $this->getTicketStatusCount('done');
        // $data = [
        //   {'name': 'open', 'count': $open},
        //   {'name': 'pending', 'count': $pending},
        //   {'name': 'in_progress', 'count': $$in_progress},
        //   {'name': 'done', 'count': $done}
        // ];

        // return response()->json([]);
    }

    public function kanban(Request $request)
    {


        $params = json_decode($request->params);


        $search = false;

        // $tickets = Ticket::with(['assigns:users.id,users.name','tracking' => function($q){
        //     $q->whereNull('end_at');
        // },'project.owner']);


        $tickets = Ticket::with(['assigns:users.id,users.name', 'project']);


        if (isset($params->number) && trim($params->number) != '') {
            $search = true;
            $tickets->where('number', 'like', '%' . $params->number . '%');
        }

        if (isset($params->title) && trim($params->title) != '') {
            $search = true;
            $tickets->where('name', 'like', '%' . $params->title . '%');
        }


        if (isset($params->status) && trim($params->status) != '') {
            $search = true;
            if (isset($params->status)) {
                $params->status = explode(',', $params->status);
            }
            $tickets->whereIn('status_id', $params->status);
        }


        if (isset($params->project) && trim($params->project->id) != '') {
            $search = true;
            $tickets->where('project_id', $params->project->id);
        }


        if (isset($params->user) && trim($params->user->id) != '') {
            $search = true;
            $tickets->wherehas('assigns', function ($assign) use ($params) {
                $assign->where('user_id', $params->user->id);
            });
        }


        if (isset($params->client) && trim($params->client->id) != '') {
            $search = true;

            $tickets->wherehas('project.owner', function ($client) use ($params) {
                $client->where('owner_id', $params->client->id);
            });
        }


        $tickets = $tickets->orderby('id', 'desc');


        // $opened  = $tickets->filter(function($ticket){
        //     return $ticket->status_id == 1;
        // });
        // $pending  = $tickets->filter(function($ticket){
        //     return $ticket->status_id == 2;
        // });
        // $inprogress  = $tickets->filter(function($ticket){
        //     return $ticket->status_id == 3;
        // });
        // $done  = $tickets->filter(function($ticket){
        //     return $ticket->status_id == 4;
        // });

        $openClonedQuery = clone $tickets;
        $pendingClonedQuery = clone $tickets;
        $inprogressClonedQuery = clone $tickets;
        $doneClonedQuery = clone $tickets;

        $opened = $openClonedQuery->where('status_id', 1)->paginate(10);
        $pending = $pendingClonedQuery->where('status_id', 2)->paginate(10);
        $inprogress = $inprogressClonedQuery->where('status_id', 3)->paginate(10);
        $done = $doneClonedQuery->where('status_id', 4)->paginate(10);


        $kanban = [
            'columns' => [
                ['title' => 'Open', 'tickets' => TicketResource::collection($opened)],
                ['title' => 'Pending', 'tickets' => TicketResource::collection($pending)],
                ['title' => 'In Progress', 'tickets' => TicketResource::collection($inprogress)],
                ['title' => 'Done', 'tickets' => TicketResource::collection($done)]
            ],
            'current_page' => request()->page,
            'pages' => ceil($tickets->count() / 10),
            'search' => $search,
            'total' => $tickets->count()
        ];
        return $kanban;
    }


    public function users()
    {
        return User::where('type', 'regular-user')->get()->map(function ($user) {
            return
                [
                    'id' => $user->id,
                    'name' => $user->name
                ];
        });
    }

    public function clients()
    {
        return User::where('type', 'client')->get(['name', 'id'])->map(function ($user) {
            return
                [
                    'id' => $user->id,
                    'name' => $user->name
                ];
        });
    }

    public function projects()
    {
        return Project::get(['name', 'id']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(AddTicketRequest $request)
    {

        $input = $request->validated();
        $input['created_at'] = Carbon::now();
        $input['created_by'] = auth()->user()->id;
        $input['updated_at'] = Carbon::now();
        $input['updated_by'] = auth()->user()->id;
        $input['email_id'] = '0';
        if (!isset($input['estimated_time'])) {
            $input['estimated_time'] = 0;
        }
        try {

            unset($input['owner_id']);
            unset($input['manager_id']);
            $ticket = Ticket::create($input);
            if ($request->owner_id && is_array($request->owner_id)) {
                $attached = $ticket->owner()->sync($request->owner_id);
            }

            if ($request->assigns && is_array($request->assigns)) {
                $ticket->assigns()->sync(collect($request->assigns)->map(function ($assign) {
                    return gettype($assign) == "integer" ? $assign : $assign['id'];
                }));
            }


        } catch (\Exception $ex) {
            return $ex->getMessage();
            throw new ItemNotCreatedException('Ticket');
        }

        return $this->sendResponse(new TicketResource($ticket->load('project', 'owner')), 'Ticket created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(ViewTicketRequest $request, $id)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($id);
        if (is_null($ticket)) {
            throw new ItemNotFoundException($id);
        } else {
            $ticketDate = $ticket->updated_at;
            $ticket->read = 1;
            $ticket->updated_at = $ticketDate;
            $ticket->save();
        }


        return $this->sendResponse(new TicketResource($ticket->load('tasks', 'project', 'ticketContract.items', 'ticketContract.user.metadata')), 'Ticket retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update(UpdateTicketRequest $request, $id)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($id);
        $status_id = $ticket->status_id;
        if (!can_permission('edit', 'ticket', $ticket->created_by)) {
            throw new ItemNotUpdatedException('Ticket');
        }


        if (!$ticket) {
            throw new ItemNotFoundException($id);
        }

        $ticket->updated_at = Carbon::now();
        $ticket->updated_by = auth()->user()->id;
        if (!isset($request->project_id)) {
            $ticket->project_id = null;
        }

        try {
            $input = $request->validated();
            unset($input['owner_id']);
            unset($input['manager_id']);
            if (isset($input['description']) && trim($input['description']) != "")
                $input['description'] = $this->replace_images($input['description'], $id);

            $updated = $ticket->fill($input)->save();

            $attached = [];
            if ($request->manager_id && is_array($request->manager_id)) {
                $attached = $ticket->manager()->sync($request->manager_id)['attached'];
                $ticket->manager()->sync($request->manager_id);
            }
            if (count($attached) > 0) {
                TicketAssignJob::dispatch($attached, $ticket);
                $assigns = User::find($attached);

                if ($assigns->count() > 0) {
                    \Notification::send(getRoleUsers('Full Administrator'), new AssignTicketNotification($ticket, auth()->user(), $assigns));
                }

            }


            if ($request->owner_id && is_array($request->owner_id)) {
                $ticket->owner()->sync($request->owner_id);
            }

            if ($request->assigns && is_array($request->assigns)) {
                $ticket->assigns()->sync(collect($request->assigns)->map(function ($assign) {
                    return gettype($assign) == "integer" ? $assign : $assign['id'];
                }));

                $assigns = User::whereIn('id', $request->assigns)->where('type', 'regular-user')->get();
                if ($ticket->project) {
                    \App\Jobs\Project\ProjectAssignJob::dispatch($assigns, $ticket->project, auth()->user());
                }
            }

            if ($request->client) {
                \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $ticket->owner()->sync(User::find($request->client));
                \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            }

        } catch (Exception $ex) {
            throw new ItemNotUpdatedException('Ticket');
        }

        if (!$updated)
            throw new ItemNotUpdatedException('Ticket');


        if ($request->id != $request->target && $request->order) $this->move($request->id, $request->direction, $request->target);
        if ($request->kanban) {
            $ticket->old_status_id = $status_id;
        }
        return $this->sendResponse(new TicketResource($ticket->load('assigns:tickets_assigns.id,name')), 'Ticket updated successfully.');
    }


    public function replace_images($description, $id)
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($description);

        $imgs = $doc->getElementsByTagName('img');
        $count = 1;
        $images = [];
        foreach ($imgs as $img) {
            $img_arr = explode(',', $img->getAttribute("src"));
            if (strpos($img_arr[0], 'base64') > -1) {
                $images[$count]['content'] = $img_arr[1];
                $images[$count]['extension'] = str_replace(';base64', '', explode('/', $img_arr[0])[1]);
            }
            $count++;
        }

        $xpath = new \DOMXPath($doc);
        $count = 1;
        foreach ($xpath->query('//img') as $img) {
            $img_arr = explode(',', $img->getAttribute("src"));

            if (strpos($img_arr[0], 'base64') > -1) {
                $new = $doc->createTextNode("[image-$count]");
                $img->parentNode->replaceChild($new, $img);
            }
            $count++;
        }


        $new_desc = $doc->saveHTML();
        libxml_use_internal_errors(false);
        foreach ($images as $key => $image) {

            $image_url = '/tickets/' . $id . '/image-' . mt_rand(100000, 999999) . "." . $image['extension'];
            $attachmentPath = storage_path('app/public' . $image_url);
            $dirName = dirname($attachmentPath);

            if (!is_dir($dirName))
                mkdir($dirName, 0755, true);

            $fp = fopen($attachmentPath, "wb");
            file_put_contents($attachmentPath, base64_decode($image['content']));
            fclose($fp);
            $new_desc = str_replace("[image-$key]", '<img src="' . url('storage' . $image_url) . '" />', $description);
        }

        return $new_desc;
    }

    public function move($id, $direction, $target)
    {


        $target_order = Ticket::find($target)->order_column;
        // $max_order = Ticket::find($id)->order_column;
        // \DB::enableQueryLog();
        if ($direction == "up") {
            Ticket::withoutGlobalScopes(['collection'])->withTrashed()->where('id', '<', $id)->where('id', '>=', $target_order)->increment("order_column");
            Ticket::withoutGlobalScopes(['collection'])->withTrashed()->where('id', $id)->update(["order_column" => $target_order]);

        } else {
            Ticket::withoutGlobalScopes(['collection'])->withTrashed()->where('id', '>', $id)->where('id', '<=', $target_order)->update(["order_column" => \DB::raw('GREATEST(IF(order_column > 1 ,order_column - 1, 1) , 1)')]);
            Ticket::withoutGlobalScopes(['collection'])->withTrashed()->where('id', $id)->update(["order_column" => $target_order]);
        }
        // dd(\DB::getQueryLog());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(DeleteTicketRequest $request, $id)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($id);

        if (!can_permission('delete', 'ticket', $ticket->created_by)) {
            throw new ItemNotDeletedException('Ticket');
        }

        if (is_null($ticket)) {
            throw new ItemNotFoundException($id);
        }

        if ($ticket->tasks->isNotEmpty()) {
            foreach ($ticket->tasks as $task) {

                $images = $this->get_images_from_content($task->description);
                foreach ($images as $image_src) {
                    $image_url = explode("/storage/", $image_src)[1];
                    Storage::disk('public')->delete($image_url);
                }


                $task->delete();
            }
        }

        try {
            if ($ticket->status_id == 3) {
                return $this->sendError('ticket is in progress', '', 500);
            } else {
                $ticket->delete();

                $images = $this->get_images_from_content($ticket->description);

                foreach ($images as $image_src) {
                    $image_url = explode("/storage/", $image_src)[1];
                    Storage::disk('public')->delete($image_url);
                }


            }

        } catch (\Throwable $th) {
            throw new ItemNotDeletedException('Tracking_task');
        }

        return $this->sendResponse(new TicketResource($ticket), 'Ticket deleted successfully.');
    }


    public function get_images_from_content($content)
    {

        $doc = new \DOMDocument();
        $doc->loadHTML($content);
        $xpath = new \DOMXPath($doc);
        $imges = [];
        foreach ($xpath->query('//img') as $img) {
            $imges[] = $img->getAttribute("src");
        }
        return $imges;
    }

    public function getTicketsByProjectId($id, ListTicketRequest $request)
    {
        $ticket = Ticket::whereHas('project', function ($query) use ($id) {
            $query->where('id', $id);
        })->latest()->get();

        if (is_null($ticket)) {
            throw new ItemNotFoundException($id);
        }

        return $this->sendResponse(TicketResource::collection($ticket), 'Tickets retrieved successfully.');
    }

    public function getTicketsCountPerClient($id)
    {
        $user = User::find($id);
        if ($user->type == 'client') {
            // $allTickets = Ticket::withoutGlobalScopes(['collection'])->with('project')->whereHas('project.owner', function ($query)  use ($clientId) {
            //     $query->where('owner_id', $clientId);
            //   });
            $allTickets = Ticket::whereHas('owner', function ($query) use ($id) {
                $query->where('owner_id', '=', $id);
            });
            $ticketsNumber = $allTickets->count();
            $openTickets = $allTickets->where('status_id', 1)->count();
            $allTickets = Ticket::whereHas('owner', function ($query) use ($id) {
                $query->where('owner_id', '=', $id);
            });
            $pendingTickets = $allTickets->where('status_id', 2)->count();
            $allTickets = Ticket::whereHas('owner', function ($query) use ($id) {
                $query->where('owner_id', '=', $id);
            });
            $doingTickets = $allTickets->where('status_id', 3)->count();
            $allTickets = Ticket::whereHas('owner', function ($query) use ($id) {
                $query->where('owner_id', '=', $id);
            });
            $doneTickets = $allTickets->where('status_id', 4)->count();
        } else {
            $allTickets = Ticket::whereHas('manager', function ($query) use ($id) {
                $query->where('manager_id', '=', $id);
            });
            $done = $allTickets;
            $ticketsNumber = $allTickets->count();
            $openTickets = $allTickets->where('status_id', 1)->count();
            $allTickets = Ticket::whereHas('manager', function ($query) use ($id) {
                $query->where('manager_id', '=', $id);
            });
            $pendingTickets = $allTickets->where('status_id', 2)->count();
            $allTickets = Ticket::whereHas('manager', function ($query) use ($id) {
                $query->where('manager_id', '=', $id);
            });
            $doingTickets = $allTickets->where('status_id', 3)->count();
            $allTickets = Ticket::whereHas('manager', function ($query) use ($id) {
                $query->where('manager_id', '=', $id);
            });
            $doneTickets = $allTickets->where('status_id', 4)->count();
        }
        return $this->sendResponse(['ticketsNumber' => $ticketsNumber,
            'openTickets' => $openTickets,
            'pendingTickets' => $pendingTickets,
            'doingTickets' => $doingTickets,
            'doneTickets' => $doneTickets,], 'Tickets Number retrieved successfully.');
    }

    public function getTicketsPerClient($id)
    {
        //$tickets = Ticket::withoutGlobalScopes(['collection'])->with('project:id,name')->whereHas('project.owner', function ($query)  use ($clientId) {
        //$query->where('owner_id', $clientId);
        //})->paginate();

        $user = User::find($id);
        if ($user->type == 'client') {
            $tickets = Ticket::whereHas('owner', function ($query) use ($id) {
                $query->where('owner_id', '=', $id);
            })->paginate();
        } else {
            $tickets = Ticket::whereHas('manager', function ($query) use ($id) {
                $query->where('manager_id', '=', $id);
            })->paginate();
        }

        return $this->sendResponse(new TicketCollection($tickets), 'Tickets retrieved successfully.');
    }


    public function starttime(Request $request)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($request->ticket_id);


        if (!$ticket->tracking) {
            return $this->sendResponse(0, 'starting time.');
        }

        $stopped = $ticket->tracking()->whereNull('end_at')->get();

        if ($stopped->count() <= 0) {
            $ticket->tracking()->create([
                'start_at' => date('Y-m-d H:i:s', time()),
                'user_id' => auth()->user()->id,
            ]);
        }

        return $this->sendResponse($ticket->all_time(), 'starting time.');
    }

    public function endtime(Request $request)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($request->ticket_id);
        $time = $ticket->tracking()->whereNull('end_at')->first();
        $count_time = time() - strtotime($time->start_at);
        $time->update([
            'end_at' => date('Y-m-d H:i:s', time()),
            'count_time' => $count_time
        ]);
    }


    public function getTrackHistory($ticket_id)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($ticket_id);
        return $ticket && $ticket->tracking ? $ticket->tracking : [];
    }

    public function editTicketTrackHistory(Request $request)
    {

        $track = TrackTicket::find($request->id);
        $start_at_time = strtotime($request->start_at);
        $end_at_time = strtotime($request->end_at);
        $track->update([
            'start_at' => date('Y-m-d H:i:s', $start_at_time),
            'end_at' => date('Y-m-d H:i:s', $end_at_time),
            'count_time' => $end_at_time - $start_at_time,
        ]);
        return $this->sendResponse($track, 'Track updated successfully.');
    }

    public function deleteTrackHistory($track_id)
    {
        $track = TrackTicket::find($track_id);
        $track->delete();
        return $this->sendResponse('Track deleted successfully', 'Track deleted successfully.');
    }

    public function saveTrack(Request $request)
    {
        $request->merge(['user_id' => auth()->user()->id]);
        $start_at_time = strtotime($request->start_at);
        $end_at_time = strtotime($request->end_at);
        $request->merge(['count_time' => $end_at_time - $start_at_time]);


        TrackTicket::create($request->all());
        return $this->sendResponse('Track saved successfully', 'Track saved successfully.');

    }


    public function copyticket(Ticket $ticket)
    {
        return $this->sendResponse(new TicketResource($ticket->copy()), 'ticket copied successfully.');
    }

    public function changeduedate(Request $request)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($request->ticket_id);
        if (is_null($ticket)) {
            throw new ItemNotFoundException($id);
        }
        $datetime = date('Y-m-d H:i', strtotime($request->ticket_date . " " . $request->ticket_time));
        $ticket->update(['due_date' => $datetime]);
        return $this->sendResponse(new TicketResource($ticket), 'ticket due date saved successfully.');

    }

    public function getTicketDescription($id)
    {
        $ticket = Ticket::withoutGlobalScopes(['collection'])->find($id);
        return $this->sendResponse(['description' => $ticket->description], 'Track saved successfully.');
    }

    public function get_ticket_collection(Request $request)
    {
        $Ticket_Collection = Ticket_Collection::paginate();
        return $this->sendResponse($Ticket_Collection, 'Ticket collection saved successfully.');
    }

    public function create_ticket_collection(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:ticket_collection,email',
            'collection' => 'required|unique:ticket_collection,collection',
        ]);
        $Ticket_Collection = Ticket_Collection::create($validated);

        $oldtickets = Ticket::withoutGlobalScopes(['collection'])->wherehas("owner", function ($own) use ($validated) {
            $own->where('email', $validated['email']);
        });
        $oldtickets->update(["collection" => $validated['collection']]);

        return $this->sendResponse(['Ticket_Collection' => $Ticket_Collection], 'Ticket collection saved successfully.');
    }


    public function update_ticket_collection(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:ticket_collection,email,' . $request->id,
            'collection' => 'required|unique:ticket_collection,collection,' . $request->id,
        ]);
        $Ticket_Collection = Ticket_Collection::find($request->id);
        if (!$Ticket_Collection) {
            throw new ItemNotFoundException($request->id);
        }

        $oldtickets = Ticket::withoutGlobalScopes(['collection'])->wherehas("owner", function ($own) use ($Ticket_Collection) {
            $own->where('email', $Ticket_Collection->email);
        });
        $oldtickets->update(["collection" => null]);


        $Ticket_Collection->update($validated);

        $oldtickets = Ticket::withoutGlobalScopes(['collection'])->wherehas("owner", function ($own) use ($validated) {
            $own->where('email', $validated['email']);
        });
        $oldtickets->update(["collection" => $validated['collection']]);


        return $this->sendResponse(['Ticket_Collection' => $Ticket_Collection], 'Ticket collection saved successfully.');
    }


    public function delete_ticket_collection(Request $request)
    {
        $ticket_Collection = Ticket_Collection::find($request->id);
        if (!$ticket_Collection) {
            throw new ItemNotFoundException($request->id);
        }


        $oldtickets = Ticket::withoutGlobalScopes(['collection'])->wherehas("owner", function ($owne) use ($ticket_Collection) {
            $owne->where('email', $ticket_Collection->email);
        })->update(["collection" => null]);


        $ticket_Collection->delete();

        return $this->sendResponse($request->id, 'Ticket collection deleted successfully.');
    }

    public function addTicketToArchive(Request $request)
    {

        if (count($request->selected) > 1) {
            foreach ($request->selected as $ticket_id) {
                $ticket = Ticket::find($ticket_id);
                $ticket->archive = 1;
                $ticket->save();
                \Notification::send(getRoleUsers('Full Administrator'), new ArchiveTicketNotification($ticket, auth()->user()));

            }
            return $this->sendResponse(null, 'Succssefuly add tickets to archive.');
        } elseif (count($request->selected) == 1) {
            $ticket = Ticket::find($request->selected[0]);
            $ticket->archive = 1;
            $ticket->save();
            \Notification::send(getRoleUsers('Full Administrator'), new ArchiveTicketNotification($ticket, auth()->user()));
            return $this->sendResponse(null, 'Succssefuly add ticket to archive.');
        }
    }

    public function removeTicketFromArchive(Request $request)
    {

        if (count($request->selected) > 1) {
            foreach ($request->selected as $ticket_id) {
                $ticket = Ticket::find($ticket_id);
                $ticket->archive = null;
                $ticket->save();
            }
            return $this->sendResponse(null, 'Succssefuly remove tickets from archive.');
        } elseif (count($request->selected) == 1) {
            $ticket = Ticket::find($request->selected[0]);
            $ticket->archive = null;
            $ticket->save();
            return $this->sendResponse(null, 'Succssefuly remove ticket from archive.');
        }
    }

    public function deleteMultiTickets(DeleteMultiTicketRequest $request)
    {
        $request->validated();
        foreach ($request->all() as $id) {
            $ticket = Ticket::withoutGlobalScopes(['collection'])->find($id);
            if (!can_permission('delete', 'ticket', $ticket->created_by)) {
                throw new ItemNotDeletedException('Ticket');
            }
            if (is_null($ticket)) {
                throw new ItemNotFoundException($id);
            }
            if ($ticket->tasks->isNotEmpty()) {
                foreach ($ticket->tasks as $task) {
                    $task->delete();
                }
            }
            try {
                $ticket->delete();
            } catch (\Throwable $th) {
                throw new ItemNotDeletedException('Tracking_task');
            }
        }
        return response()->json(['message' => 'Ticket/s Deleted successfully.']);
    }

    public function update_ticket_status(Ticket $ticket, $status)
    {
        $ticket->update(['status_id' => $status]);
        return response()->json(['message' => 'Ticket closed successfully.']);
    }

    public function openMultiTickets(Request $request)
    {
        Ticket::whereIn('id', $request->all())->update(['status_id' => 1]);
        return response()->json(['message' => 'Tickets opened successfully.']);
    }

    public function closeMultiTickets(Request $request)
    {
        Ticket::whereIn('id', $request->all())->update(['status_id' => 4]);
        return response()->json(['message' => 'Tickets closed successfully.']);
    }
}
