<?php

namespace Modules\Calender\Http\Controllers;
use App\Http\Controllers\API\BaseController;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use \Modules\Calender\Http\Controllers\TecseeSimpleCalDAVClient;
use it\thecsea\simple_caldav_client\CalDAVCalendar;
use it\thecsea\simple_caldav_client\CalDAVObject;
use ICal\ICal;
use Carbon\Carbon;
use Modules\Calender\Entities\Calendar;
use App\Models\User;
use DateTime;
use Modules\Calender\Events\EventAdded;
use Modules\Calender\Entities\Category;
use Modules\Calender\Http\Requests\CategoryRequest;
use Modules\Calender\Entities\Setting;
use Modules\Calender\Entities\RemoteCalendarSetting;
use Modules\Calender\Entities\CalendarFreeTime;
use Modules\Calender\Jobs\AddEventJob;
use Mail,Image;
use Illuminate\Support\Facades\Storage;

class CalendarController extends BaseController
{
    private $calendar;
    private $connected = false;
    private $events_data = [];
    public function __construct() {

        // calendar




        // $uids = [];
        // foreach ($this->events_data as $event) {
        //     $uids[] = $event->cal['VEVENT'][0]['UID'];
        // }
        // dd($uids);


       }


 
        
    public function get_url($host,$username,$password){
        $ch = curl_init($host);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    public function calendar_connect($userid){
        $this->calendar  = new TecseeSimpleCalDAVClient();
        $remote_calender = RemoteCalendarSetting::where('user_id',$userid ? $userid : auth()->user()->id)->first();
        
        if($remote_calender) {
            $this->connected = true;
            // $this->calendar->connect('https://kasmail.kasserver.com/calendars/'.$remote_calender->username.'/1',$remote_calender->username,$remote_calender->password,[]);
            // $this->calendar->setCalendar(new CalDAVCalendar('/calendars//'.$remote_calender->username.'/1'));
           // https://ion.alfacockpit.com/baikal/html/dav.php/calendars/osama/default/
            $this->calendar->connect('https://calendar.alfacockpit.com/html/dav.php/calendars/'.$remote_calender->username.'/default/',$remote_calender->username,$remote_calender->password,[]);
            $this->calendar->setCalendar(new CalDAVCalendar('/html/dav.php/calendars/'.$remote_calender->username.'/default/'));
        
        }
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index($month,$userid)
    {
        $permissions = [];
        if($userid && $userid != "null"){
            $permissions = Setting::where('user_id',auth()->user()->id)->where('calendar_user_id',$userid)->pluck('permissions')->toArray();
            if(!in_array('show',$permissions)){
                return $this->sendError([], 'Action not allowed');
            }
        }


        $userid = $userid == 'null' || !$userid || $userid == auth()->user()->id ? null : $userid;

        $this->calendar_connect($userid);
        $remote_calender = RemoteCalendarSetting::where('user_id',$userid ? $userid : auth()->user()->id)->first();
        $etags      = Calendar::pluck('etag')->toArray();
        $events = [];
        if($this->connected) {
            $events     = $this->calendar->getEvents();
        }


        if($userid) {
            Calendar::where('user_id',$userid)->where('approved',1)->delete();        
        }else {
            Calendar::where('user_id',auth()->user()->id)->where('approved',1)->delete();        
        }

        $remote_etags = [];
        foreach($events as $event){
            $event_array    = [];
            $remote_etags[] = $event->getEtag();
            $event_data = new ICal($this->get_url($event->getHref(),$remote_calender->username,$remote_calender->password));
            // if(!in_array($event->getEtag(),$etags)){
                $event = array(
                    'user_id'              => $userid ? $userid : auth()->user()->id,
                    'title'                => isset($event_data->cal['VEVENT'][0]['SUMMARY']) ? $event_data->cal['VEVENT'][0]['SUMMARY'] : '',
                    'date'                 => date('Y-m-d',strtotime($event_data->cal['VEVENT'][0]['DTSTART'])),
                    'description'          => isset($event_data->cal['VEVENT'][0]['DESCRIPTION']) ?$event_data->cal['VEVENT'][0]['DESCRIPTION'] : '' ,
                    'from'                 => date('H:i:s',strtotime($event_data->cal['VEVENT'][0]['DTSTART_tz'])),
                    'to'                   => date('H:i:s',strtotime($event_data->cal['VEVENT'][0]['DTEND_tz'])),
                    'location'             => isset($event_data->cal['VEVENT'][0]['LOCATION']) ? $event_data->cal['VEVENT'][0]['LOCATION'] : '',
                    'href'                 => $event->getHref(),
                    'etag'                 => $event->getEtag(),
                    //'attachment'    => isset($event_data->cal['VCALENDAR']['ATTACH'])  ? $event_data->cal['VCALENDAR']['ATTACH'] : '',
                    'attachment'           => isset($event_data->cal['VEVENT'][0]['ATTACH'])  ? $event_data->cal['VEVENT'][0]['ATTACH'] : '',
                    'attachment_mime_type' => isset($event_data->cal['VEVENT'][0]['X-ATTACHTYPE'])  ? $event_data->cal['VEVENT'][0]['X-ATTACHTYPE'] : '',
                    'attachment_name'      => isset($event_data->cal['VEVENT'][0]['X-ATTACHNAME'])  ? $event_data->cal['VEVENT'][0]['X-ATTACHNAME'] : '',
                );
                Calendar::create($event);
            // }
        }


        Calendar::whereNotIn('etag',$remote_etags)->where('approved',1)->delete();

        if($userid) {
            $user = User::find($userid);
            if(!$user){
                return $this->sendError(['success'=>false], 'Errors');
            }
        }


        if(!$userid) {
            $calendars = Calendar::with(['attendees'])->whereMonth('date',$month)->where('user_id',auth()->user()->id)
            ->orWhereHas('attendees',function($query){
                $query->where('user_id',auth()->user()->id);
            })
            ->get();
        } else {
            //$permissions = Setting::where('calendar_user_id',$userid)->where('user_id',auth()->user()->id)->pluck('id')->toArray();

            $calendars = Calendar::with(['attendees'])->whereMonth('date',$month)->where('user_id',$userid)
            ->orWhereHas('attendees',function($query) use($userid){
                $query->where('user_id',$userid);
            })
            ->get();
        }



        $events = $calendars->map(function($event) use($userid,$permissions) {
            $start = $event->all_day ? $event->date." 00:00" : $event->date." ".$event->from;
            $end   = $event->all_day ? $event->date." 23:59" : $event->date." ".$event->to;
            return [
                'id'                 =>  $event->id,
                'title'              =>  !$userid || in_array('showevents',$permissions) ? $event->title : 'busy',
                'date'               =>  $event->date,
                'start'              =>  $start,
                'end'                =>  $end,
                'all_day'            =>  $event->all_day,
                'location'           =>  $event->location,
                'description'        =>  $event->description,
                'repeat'             =>  $event->repeat,
                'repeat_type'        =>  $event->repeat_type,
                'end_at'             =>  $event->end_at,
                'category_id'        =>  $event->category_id,
                'attendees'          =>  $event->attendees->map(function($attendee){
                    return $attendee->user_id;
                }),
                'backgroundColor'    =>  $event->approved ? '#ffffff' : '#ff0000',
                'borderColor'        =>  '#ffffff',
                'textColor'          =>  $event->approved ? '#000000' : '#ffffff',
                'approved'           =>  $event->approved,
                'name'               =>  $event->name,
                'email'              =>  $event->email,
                'mobile'             =>  $event->mobile,
                'attachment'         =>  $event->attachment,
                'attachment_name'    =>  $event->attachment_name,
                'attachment_type'    =>  $event->attachment_mime_type,
            ];
        });

        // foreach ($this->events_data as $event) {
        //     $events[] = $event;
        // }

        return $this->sendResponse(['events'=>$events], 'Events retrieved successfully.');
    }


    public function addevent(Request $request)
    {        


        $permissions = [];
        if($request->owner){
            $permissions = Setting::where('user_id',auth()->user()->id)->where('calendar_user_id',$request->owner)->pluck('permissions')->toArray();
            if(!in_array('edit',$permissions)){
                return $this->sendError([], 'Action not allowed');
            }
        }

        
        $user_id = $request->owner ? $request->owner : auth()->user()->id;
        $request->merge(['user_id'    =>  $user_id]);

        $request->merge(['creator_id' => auth()->user()->id]);
        $request->merge(['day_name'   => date('l', strtotime($request->date))]);
        $request->merge(['date'   => date('Y-m-d', strtotime($request->date))]);


        $event_row = Calendar::create($request->all());
        $event = $this->prepareEvent($event_row);

        $attendees = [];
        $request->attendees = explode(",",$request->attendees);
        if(count($request->attendees) > 0){
            foreach($request->attendees as $attendee){
                $attendees[] = [
                    'event_id' => $event_row->id,
                    'user_id' => $attendee,
                ];
            }
        }
        if(count($attendees) > 0) {
            $event['attendees'] = User::whereIn('id',$request->attendees)->get()->map(function($attend){
                return [
                    'id'    => $attend->id,
                    'email' => $attend->email,
                    'name'  => $attend->name
                ];
                
            });
        }
        foreach ($event['attendees'] as  $attend) {
            if($attend['id'] == auth()->user()->id) continue;
            event(new EventAdded($attend['id'],$event));
        }
        $event_row->attendees()->createMany($attendees);
        $this->calendar_connect($user_id);


        if($this->connected){
            $eol = "\r\n";
            $cal_event = 
            "BEGIN:VCALENDAR" . $eol .
            "TZID:Egypt Standard Time". $eol .
            "TZOFFSETFROM:+0200". $eol .
            "TZOFFSETTO:+0200". $eol .
            "VERSION:2.0" . $eol .
            "PRODID:-//project/author//NONSGML v1.0//EN" . $eol .
            "CALSCALE:Europe/Berlin" . $eol .
            "BEGIN:VEVENT" . $eol .
            "UID:" . uniqid() . $eol .
            "DTSTAMP:" . $this->dateToCal(time()) . $eol .
            "DESCRIPTION:" . htmlspecialchars(strip_tags($event['description'])) . $eol .
            "SUMMARY:" . htmlspecialchars($event['title']) . $eol;
            
            if(!$request->all_day){
                $cal_event .= "DTEND;TZID='Egypt Standard Time':"   . $this->dateToCal(strtotime($event['end'])) . $eol ;
                $cal_event .= "DTSTART;TZID='Egypt Standard Time':" . $this->dateToCal(strtotime($event['start'])) . $eol;
            }else {
                $cal_event .= "DTSTART;VALUE=DATE:". $this->dateToCalDate(strtotime($event['date'])) . $eol ;
                $cal_event .= "DTEND;VALUE=DATE:". $this->dateToCalDate(strtotime($event['date']. ' +1 day'))   . $eol ;
            }

            if(count($event['attendees']) > 0) {
                foreach ($event['attendees'] as  $attend) {
                    if($attend['id'] == auth()->user()->id) continue;
                        $cal_event .= 'ATTENDEE;CN="'.$attend['name'].'";CUTYPE=INDIVIDUAL;PARTSTAT=ACCEPTED:
                        mailto:'.$attend['email']. $eol;
                }
            }
    

            $filedata = " ";
            if($request->hasFile('attachment')){
                // $full_url = url('storage/calendar_attachments/'.$url);
                $url        = $request->attachment->store('/','calendar_attachments');
                $filedata   = base64_encode(file_get_contents(Storage::disk('calendar_attachments')->path($url)));
                $cal_event .= "ATTACH;ENCODING=BASE64;VALUE=BINARY;X-FILENAME=".$request->attachment->getClientOriginalName().":".$filedata. $eol ;
                $cal_event .= "X-ATTACHTYPE:".$request->attachment->getMimeType(). $eol ;
                $cal_event .= "X-ATTACHNAME:".$request->attachment->getClientOriginalName(). $eol ;
                Storage::disk('calendar_attachments')->delete($url);
            }
            
            $cal_event .= "END:VEVENT" . $eol .
            "END:VCALENDAR";
            $cal = $this->calendar->create($cal_event);
 
            $event_row->update([
                'etag'                 => $cal->getEtag(),
                'href'                 => $cal->getHref(),
                'attachment'           => $filedata,
                'attachment_mime_type' => $request->hasFile('attachment') ? $request->attachment->getMimeType() : "",
                'attachment_name'      => $request->hasFile('attachment') ? $request->attachment->getClientOriginalName() : ""
            ]);
    }

        return $this->sendResponse($event, 'Event created successfully.');
    }

    public function dateToCal($timestamp) {
        return date('Ymd\THis\Z', $timestamp);
    }
      
    public function dateToCalDate($timestamp) {
        return date('Ymd', $timestamp);
    }

    public function escapeString($string) {
        return preg_replace('/([\,;])/','\\\$1', $string);
    }    
      

    private function prepareEvent($event_row) {
        $event_row->date = $event_row->all_day ? $event_row->date : date('Y-m-d',strtotime($event_row->date));
        $start = $event_row->all_day ? $event_row->date : str_replace('00:00:00','',$event_row->date).' '.$event_row->from;
        $end   = $event_row->all_day ? $event_row->date : str_replace('00:00:00','',$event_row->date).' '.$event_row->to;
        return [
            'id'                 =>  $event_row->id,
            'user_id'            =>  $event_row->user_id,
            'title'              =>  $event_row->title,
            'date'               =>  $event_row->date,
            'start'              =>  $start,
            'end'                =>  $end,
            'backgroundColor'    =>  '#ffffff',
            'borderColor'        =>  '#ffffff',
            'textColor'          =>  '#000000',
            'all_day'            =>  $event_row->all_day,
            'location'           =>  $event_row->location,
            'description'        =>  $event_row->description,
            'repeat'             =>  $event_row->repeat,
            'repeat_type'        =>  $event_row->repeat_type,
            'end_at'             =>  $event_row->end_at,
            'category_id'        =>  $event_row->category_id,
            'attachment'         =>  $event_row->attachment,
            'attachment_name'    =>  $event_row->attachment_name,
            'attachment_type'    =>  $event_row->attachment_type,
            'attendees'          =>  $event_row->attendees->map(function($attendee){
                return $attendee->user_id;
            }),

        ];
    }

    public function deleteEvent(request $request)
    {

        $event     = Calendar::find($request->id);
        $this->calendar_connect($request->user_id);
        if($this->connected){
          $delete = $this->calendar->delete($event->href);
        //   $this->delete_attachment($event);

        }
        $event->delete();
        return $this->sendResponse(['id'=>$request->id], 'Event deleted successfully.');
    }
    // private function delete_attachment($event) {
    //     if($event->attachment){
    //         $path_arr  = explode("/",$event->attachment);
    //         $path      = end($path_arr);
    //         Storage::disk("calendar_attachments")->delete($path);
    //     }
    // }

    public function approveEvent(Request $request){
        $event = Calendar::find($request->id);
        $event_ar = $this->prepareEvent($event);
        $this->calendar_connect($event['user_id']);
        if($this->connected){
            $eol = "\r\n";
            $cal_event = "BEGIN:VCALENDAR" . $eol .
            "TZNAME:EET". $eol .
            "VERSION:2.0" . $eol .
            "PRODID:-//project/author//NONSGML v1.0//EN" . $eol .
            "CALSCALE:GREGORIAN" . $eol .
            "BEGIN:VEVENT" . $eol .
            "UID:" . uniqid() . $eol .
            "DESCRIPTION:" . htmlspecialchars(strip_tags($event_ar['description'])) . $eol .
            "SUMMARY:" . htmlspecialchars($event_ar['title']) . $eol .
            "DTSTART:" . $this->dateToCal(strtotime($event_ar['start'])) . $eol .
            "DTEND:" . $this->dateToCal(strtotime($event_ar['end'])) . $eol .
            "DTSTAMP:" . $this->dateToCal(time()) . $eol .
            "END:VEVENT" . $eol;

            if($event_ar['attachment'] != '') {
                $cal_event .= "ATTACH;ENCODING=BASE64;VALUE=BINARY;X-FILENAME=".$event_ar['attachment'].":".$filedata. $eol.
                "X-ATTACHTYPE:".$event_ar['attachment_type']. $eol .
                "X-ATTACHNAME:".$event_ar['attachment_name']. $eol;
            }


            $cal_event .= "END:VCALENDAR";
           $cal = $this->calendar->create($cal_event);

          $event->update([
            "approved" => 1
          ]);

        }

        return $this->sendResponse($event->id, 'Event approved successfully.');
    }


    public function rejectEvent(Request $request){
        $event = Calendar::find($request->id);
        if($event){
            $event->delete();
            Mail::send(
                'emails.reject-event',
                [
                    'name' => $event->name,
                    'date' => $event->date,
                    'from' => $event->from,
                    'to'   => $event->to,
                ],
                function ($message) use ($event) {
                    $message->from('no-replay@tecsee.de');
                    $message->to($event->email)
                        ->subject('Your event declined');
                }
            );
        }
        return $this->sendResponse($event->id, 'Event rejected successfully.');
    }

    public function updateEvent(request $request) {

        $eventObj = Calendar::find($request->id);

        $request->merge(['day_name'   => date('l', strtotime($request->date))]);
        $request->merge(['all_day'   => $request->all_day ? 1 : 0]);

        $request->merge(['date'   => date('Y-m-d', strtotime($request->date))]);

        $eventObj->update($request->all());



        $attendees = [];
        if($request->attendees && count($request->attendees) > 0){
            foreach($request->attendees as $attendee){
                $attendees[] = [
                    'event_id' => $event->id,
                    'user_id' => $attendee,
                ];
            }
        }



        $eventObj->attendees()->delete();
        $eventObj->attendees()->createMany($attendees);

        $this->calendar_connect($request->user_id);




        $event = $this->prepareEvent($eventObj);

        if($this->connected){
            $eol = "\r\n";
            $cal_event = "BEGIN:VCALENDAR" . $eol .
            "TZNAME:EET". $eol .
            "VERSION:2.0" . $eol .
            "PRODID:-//project/author//NONSGML v1.0//EN" . $eol .
            "CALSCALE:GREGORIAN" . $eol .
            "BEGIN:VEVENT" . $eol .
            "UID:" . uniqid() . $eol .
            "DTSTAMP:" . $this->dateToCal(time()) . $eol .
            "DESCRIPTION:" . htmlspecialchars(strip_tags($event['description'])) . $eol .
            "SUMMARY:" . htmlspecialchars($event['title']) . $eol ;
            
            // "DTEND:" . $this->dateToCal(strtotime($event['end'])) . $eol .
            // "DTSTART:" . $this->dateToCal(strtotime($event['start'])) . $eol ;
            
            if(!$request->all_day){
                $cal_event .= "DTEND;TZID='Egypt Standard Time':"   . $this->dateToCal(strtotime($event['end'])) . $eol ;
                $cal_event .= "DTSTART;TZID='Egypt Standard Time':" . $this->dateToCal(strtotime($event['start'])) . $eol;
            }else {
                $cal_event .= "DTSTART;VALUE=DATE:". $this->dateToCalDate(strtotime($event['date'])) . $eol ;
                $cal_event .= "DTEND;VALUE=DATE:". $this->dateToCalDate(strtotime($event['date']. ' +1 day'))   . $eol ;
            }

            
            
            
            $filedata = "";
            if($request->hasFile('attachment')){
                $url        = $request->attachment->store('/','calendar_attachments');
                $filedata   = base64_encode(file_get_contents(Storage::disk('calendar_attachments')->path($url)));
                $cal_event .= "ATTACH;ENCODING=BASE64;VALUE=BINARY;X-FILENAME=".$request->attachment->getClientOriginalName().":".$filedata. $eol ;
                $cal_event .= "X-ATTACHTYPE:".$request->attachment->getMimeType(). $eol ;
                $cal_event .= "X-ATTACHNAME:".$request->attachment->getClientOriginalName(). $eol ;
                Storage::disk('calendar_attachments')->delete($url);
            }


            
            
            $cal_event .=  "END:VEVENT" . $eol .
            "END:VCALENDAR";
            $cal = $this->calendar->delete($eventObj->href);
            $cal = $this->calendar->create($cal_event);
            
            $event['attendees'] = $request->attendees;
                // $this->delete_attachment($eventObj);
                $ev = [
                    "etag" => $cal->getEtag(),
                    "href" => $cal->getHref(),
                    "attachment" => $filedata,
                    'attachment_mime_type' => $request->hasFile('attachment') ? $request->attachment->getMimeType() : "",
                    'attachment_name'      => $request->hasFile('attachment') ? $request->attachment->getClientOriginalName() : ""
                    ];

                $eventObj->update($ev);
            }
            $event = $this->prepareEvent($eventObj);
        return $this->sendResponse(['event'=>$event], 'Event updated successfully.');
    }


    public function get_categories() {
        return $this->sendResponse(['categories'=>Category::all()], 'categories retrivet successfully.');
    }

    public function delete_category(Category $category){
      $category->delete();
      return $this->sendResponse(['category' => $category], 'category deleted successfully.');
    }

    public function add_category(CategoryRequest $request){
      $category =  Category::create($request->validated());
      return $this->sendResponse(['category' => $category], 'category created successfully.');
    }

    public function edit_category(CategoryRequest $request,$id){
        $category = Category::find($id);
        $category->update($request->validated());
        return $this->sendResponse(['category' => $category], 'category edited successfully.');
    }



    function getEmailArrayFromString($sString = '')
    {
        $sString = str_replace('ATTENDEE','', $sString);
        $sPattern = '/[\._\p{L}\p{M}\p{N}-]+@[\._\p{L}\p{M}\p{N}-]+/u';
        preg_match_all($sPattern, $sString, $aMatch);
        $aMatch = array_keys(array_flip(current($aMatch)));
        return $aMatch;
    }



    function calendar_settings(Request $request){
        Setting::where('calendar_user_id',auth()->user()->id)->delete();
        $data = null;
        foreach ($request->canshow as  $canshow) {
            $data[] = [
                'permissions'      => 'show',
                'calendar_user_id' =>  auth()->user()->id,
                'user_id'          =>  $canshow['id'],
            ];
        }
        foreach ($request->canedit as  $canedit) {
            $data[] = [
                'permissions'      => 'edit',
                'calendar_user_id' =>  auth()->user()->id,
                'user_id'          =>  $canedit['id'],
            ];
        }
        foreach ($request->canshowevents as  $canshowevents) {
            $data[] = [
                'permissions'      => 'showevents',
                'calendar_user_id' =>  auth()->user()->id,
                'user_id'          =>  $canshowevents['id'],
            ];
        }
        if($data){
            Setting::insert($data);
        }

        if($request->remotepassword || $request->remoteusername){
            $arr = ['user_id' => auth()->user()->id];
            if($request->remotepassword){
                $arr['password'] = $request->remotepassword;
            }

            if($request->remoteusername){
                $arr['username'] = $request->remoteusername;
            }
            RemoteCalendarSetting::updateOrCreate(['user_id' => auth()->user()->id],$arr);
        }    
    }


    function get_calendar_settings(Request $request){
        
        $settings = Setting::where('calendar_user_id',auth()->user()->id)->get();
        
        $canshow  = $settings->filter(function($set){
            return $set->permissions == "show";
        })->map(function($set){
            return $set->user_id;
        })->toArray();

        $canedit  = $settings->filter(function($set){
            return $set->permissions == "edit";
        })->map(function($set){
            return $set->user_id;
        })->toArray();

        $canshowevents  = $settings->filter(function($set){
            return $set->permissions == "showevents";
        })->map(function($set){
            return $set->user_id;
        })->toArray();

        $remote = [
            'username' => '',
            'password' => ''
        ];
        
        $remote_calender = RemoteCalendarSetting::where('user_id',auth()->user()->id)->first();

        if($remote_calender){
            $remote['username'] = $remote_calender->username;
            $remote['password'] = $remote_calender->password;
        }

        return $this->sendResponse(['username'=>$remote['username'],'password' => $remote['password'],'canshow' => $canshow,'canedit' => $canedit,'canshowevents' => $canshowevents], 'Event created successfully.');
    }


    public function get_show_calendars(){
        $showCalendars = [];


        if(count(auth()->user()->get_show_calendars()) > 0) {
            foreach (auth()->user()->get_show_calendars() as $key => $calendar) {
                $showCalendars[$key]['name'] = $calendar->user->name;
                $showCalendars[$key]['id']   = $calendar->user->id;
            }
        }

        $showCalendars =  array_map("unserialize", array_unique(array_map("serialize", $showCalendars)));
        return $this->sendResponse($showCalendars, 'calendars created successfully.');
    }

    public function html_calendar($user_id){
        $user            = User::FindOrFail($user_id);
        $available_times = CalendarFreeTime::where("user_id",$user->id)->get();
        $bookingTimes    = Calendar::where('user_id',$user_id)->where('date','>=', Carbon::now()->format('Y-m-d'))->where("approved",1)->get(['id','date','from','to']);

        $bookingTimes    = $bookingTimes->map(function($time){
            return Carbon::parse($time->date)->format('d-m-Y')."T".$time->from;
        });

        return view('frontend.home.calendar',compact('available_times','user','bookingTimes'));
    }


    public function ajax_calendar_add_event(Request $request){
        if($request->ajax()){
            $user  = User::FindOrFail($request->user_id);


            $full_url = "";
            if($request->hasFile('attachment')){
                $url      = $request->attachment->store('/','calendar_attachments');
                $filedata   = base64_encode(file_get_contents(Storage::disk('calendar_attachments')->path($url)));
            }


            $event = Calendar::create([
                'user_id'     => $user->id,
                'title'       => $request->title,
                'date'        => $request->date,
                'from'        => $request->start,
                'to'          => $request->end,
                'all_day'     => 0,
                'description' => $request->description,
                'name'        => $request->name,
                'email'       => $request->email,
                'mobile'      => $request->mobile,
                'approved'    => 0,
                'attachment'   => $filedata,
                'attachment_mime_type' => $request->attachment->getMimeType(),
                'attachment_name'      => $request->attachment->getClientOriginalName()
            ]);



            AddEventJob::dispatch($event);
            Storage::disk('calendar_attachments')->delete($url);

            return $event;
        }


        
    }
    
    public function add_free_times(Request $request){
        CalendarFreeTime::where("user_id",auth()->user()->id)->delete();
        $array = [];
        foreach ($request->times as $day => $values) {
            foreach ($values as $value) {
                if($value['from'] == "" || $value['to'] == "") {
                    throw new \ErrorException('Please fill all fields.');
                    return false;
                }
                $array[] = [
                    'user_id'    => auth()->user()->id,
                    'day_number' => $value['day_number'],
                    'day'        => $day,
                    'from'       => $value['from'],
                    'to'         => $value['to']
                ];
            }
        }
        CalendarFreeTime::insert($array);
    }


    public function get_free_times(Request $request){
        $times = CalendarFreeTime::where("user_id",auth()->user()->id)->get();
        return $this->sendResponse($times, 'times retrived successfully.');
    }

}
