<?php

namespace Modules\TicketCalendar\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Ticket;
use App\Http\Controllers\API\BaseController;
use Modules\TicketCalendar\Http\Requests\EventRequest;
use Modules\TicketCalendar\Entities\Event;
class TicketCalendarController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {

    }
    public function events(Ticket $ticket){
          $events = $ticket->events->map(function($event){
            return [
                'id'                 =>  $event->id,
                'title'              =>  $event->title,
                'description'        =>  $event->description,
                'date'               =>  $event->date_time,
                'backgroundColor'    =>  '#ffffff',
                'borderColor'        =>  '#ffffff',
                'textColor'          =>  '#000000',
            ];
        })->toArray();

        array_push($events,[
                // 'id'                 =>  null,
                'title'              =>  'deadline',
                'date'               =>  $ticket->due_date,
                'description'        =>  'deadline',
                'backgroundColor'    =>  '#ffffff',
                'borderColor'        =>  '#ffffff',
                'textColor'          =>  '#000000',
        ]);

        return $this->sendResponse(["events" => $events],'Events retrived succssfully');
    }


    public function add_event(Ticket $ticket,EventRequest $request){
        $event = $ticket->events()->create($request->validated());
        $event = [
                'title'              =>  $event->title,
                'date'               =>  $event->date_time,
                'description'        =>  $event->description,
                'backgroundColor'    =>  '#ffffff',
                'borderColor'        =>  '#ffffff',
                'textColor'          =>  '#000000',
        ];
        return $this->sendResponse(["event" => $event],'Event created succssfully');
    }


    public function edit_event(EventRequest $request,Ticket $ticket,Event $event){
        $event->update($request->validated());
        $event = [
                'title'              =>  $event->title,
                'date'               =>  $event->date_time,
                'description'        =>  $event->description,
                'backgroundColor'    =>  '#ffffff',
                'borderColor'        =>  '#ffffff',
                'textColor'          =>  '#000000',
        ];
        return $this->sendResponse(["event" => $event],'Event updated succssfully');
    }

    public function delete_event(Ticket $ticket,Event $event){
        $event->delete();
        return $this->sendResponse(["event" => $event->id],'Event deleted succssfully');
    }

}
