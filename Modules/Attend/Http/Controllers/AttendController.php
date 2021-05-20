<?php

namespace Modules\Attend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\API\BaseController;
use Modules\Attend\Entities\YearlyVacation;
use Modules\Attend\Entities\WeeklyVacation;
use Modules\Attend\Entities\YearlyHoliday;
use Modules\Attend\Entities\Attend;
use Modules\Attend\Entities\AttendTemp;
use Modules\Attend\Entities\Vacation;
use Modules\Attend\Http\Resources\AttendsResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail;
use App\Mail\VacationEmail;
use App\Jobs\Attendees\WorkTimeReportJob;
use App\Jobs\Attendees\AttendeesReviewJob;

use Modules\Attend\Http\Resources\AttendResource;
use Modules\Attend\Http\Resources\AttendCollection;



use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsAttendsExports;


class AttendController extends BaseController
{



    public function __construct()
    {
        $this->middleware('permission:manage-attendees|hr-assistant|hr', ['only' => ['get_employees_vacations']]);
    }


    public function add_vacation(Request $request) {
        $user = auth()->user();

        if($request->user_id && $user->hasRole('Full Administrator')) {
            $user = User::findOrFail($request->user_id);
            $request->merge(['status' => 'confirmed']);
        }

        if($request->sick_vacation == "true"){


            if (!$request->hasFile('sick_image_file')) {
                return response()->json(['upload_file_not_found'], 400);
            }

            $allowedfileExtension = ['jpg', 'png'];
            $file = $request->file('sick_image_file');
            $extension = $file->getClientOriginalExtension();
            $check = in_array($extension, $allowedfileExtension);
            if ($check) {
                $filename  = 'sick-' . time() . '.' . $extension;
                $path = 'public/sick_images/';
                $file->storeAs($path, $filename);
                $imageLink = Storage::url('sick_images/'. $filename);

                $request->merge(['sick_image' => $filename]);

            } else {
                return response()->json(['invalid_file_format'], 422);
            }


        }

        $t1 = Carbon::parse($request->day_to." ".$request->time_to);
        $t2 = Carbon::parse($request->day_from." ".$request->time_from);
        $vactiondays =  round($t1->diffInHours($t2)/24,2);


        $hours = $user->vacations->where('status','!=','rejected')->sum(function ($vacation) {
            $t1 = Carbon::parse($vacation->day_to." ".$vacation->time_to);
            $t2 = Carbon::parse($vacation->day_from." ".$vacation->time_from);
            return $t1->diffInHours($t2);
        });

        $days = round(($hours/24),2);

        $resetDays = (double)$user->vacation - (double)$days;

        if($vactiondays >  $resetDays && !$user->skip_vacation_limit) {
            return $this->sendError('you have only '.$resetDays.' day(s)', 'vacation creating Error.',500);
        }

        $vacation = $user->vacations()->create($request->all());


        if($request->sick_vacation == "true") {
            $attrs = [
                'day_from' => $request->consultation,
                'day_to'   => $request->consultation,
                'time_from'=> '00:00:01',
                'time_to'  => '23:59:59',
                'reason'   => $request->reason.' - consultation'

            ];
            $sick_vacation = $user->vacations()->create($attrs);
        }

        if($request->user_id && $request->user_id != auth()->user()->id && auth()->user()->hasRole('Full Administrator')) {
            if($user->support === 0) {
                Mail::to($user->email)->send(new VacationEmail($vacation->load('user')));
            }
        }

        Mail::to('hr@tecsee.de')->send(new VacationEmail($vacation->load('user')));
        //return $this->sendResponse(['vacation' => $vacation->load('user'),'sick_vacation' => $sick_vacation->load('user')], 'vacation created successfully.');


        return $this->sendResponse($vacation->load('user'), 'vacation created successfully.');
    }

    public function update_vacation(Request $request) {
        $vacation = Vacation::with('user')->findOrFail($request->id);



        $t1 = Carbon::parse($request->day_to." ".$request->time_to);
        $t2 = Carbon::parse($request->day_from." ".$request->time_from);
        $vactiondays =  round($t1->diffInHours($t2)/24,2);

        $t1 = Carbon::parse($vacation->day_to." ".$vacation->time_to);
        $t2 = Carbon::parse($vacation->day_from." ".$vacation->time_from);
        $vactionrestdays =  round($t1->diffInHours($t2)/24,2);

        $hours = $vacation->user->vacations->where('status','!=','rejected')->sum(function ($vacation) {
            $t1 = Carbon::parse($vacation->day_to." ".$vacation->time_to);
            $t2 = Carbon::parse($vacation->day_from." ".$vacation->time_from);
            return $t1->diffInHours($t2);
        });

        $days = round(($hours/24),2)-$vactionrestdays;

        $resetDays = (double)$vacation->user->vacation - (double)$days;

        if($vactiondays >  $resetDays  && !$vacation->user->skip_vacation_limit) {
            return $this->sendError('you have only '.$resetDays.' day(s)', 'vacation creating Error.',500);
        }

        $vacation->update($request->all());

        if($request->user_id && $request->user_id != auth()->user()->id && auth()->user()->hasRole('Full Administrator')) {
            if($vacation->user->support === 0) {
                Mail::to($vacation->user->email)->send(new VacationEmail($vacation));
            }
        }


        return $this->sendResponse($vacation->load('user'), 'vacation created successfully.');
    }






    public function get_vacations(Request $request) {
        $vacations = auth()->user()->vacations->load('user');

        if(auth()->user()->hasRole('Full Administrator')) {
            $vacations = Vacation::with('user')->get();
        }

        return $this->sendResponse($vacations, 'vacations retrieved successfully.');
    }

    public function get_employees_vacations(Request $request){
        //$vacations = Vacation::with('user')->where('status','review')->get();
        $vacations = Vacation::with('user');
        if($request->from && trim($request->from) != '')
            $vacations = $vacations->where('day_from','>=',$request->from);

        if($request->to && trim($request->to) != '')
            $vacations = $vacations->where('day_to','<=',$request->to);




        $vacations = $vacations->get();
        return $this->sendResponse($vacations, 'Employees vacations retrieved successfully.');
    }

    public function set_vacation_action(Request $request) {
        $vacation = Vacation::find($request->id);
        if(!$vacation) {
            return $this->sendError('Vacation not found');
        }

        $vacation->status = $request->action;
        $vacation->save();
        return $this->sendResponse($vacation, 'Employees vacations retrieved successfully.');

    }

    public function date_data(Request $request){
        $attends = auth()->user()->attendes()->where('date',$request->date)->first();
        return $this->sendResponse($attends, 'Attendes  retrieved successfully.');
    }

    public function set_date_data(Request $request){
        $attends = auth()->user()->attendes()->updateOrCreate(['date' => $request->form['date']],$request->form);
        return $this->sendResponse($attends, 'Attendes  retrieved successfully.');
    }


    public function get_year_data(Request $request) {
        $yearlyvacation =  YearlyVacation::where('year',$request->year)->first();
        return $this->sendResponse($yearlyvacation, 'yearly vacations retrieved successfully.');
    }

    public function set_year_data(Request $request){
        $yearlyvacation = YearlyVacation::updateOrCreate(['year' => $request->vacation['year']],$request->vacation);
        return $this->sendResponse($yearlyvacation, 'yearly vacation retrieved successfully.');
    }

    public function get_year_holidays(Request $request) {
        $YearlyHolidays =  YearlyHoliday::where('year',$request->year)->get(['id','name','from','to'])->toArray();
        return $this->sendResponse($YearlyHolidays, 'yearly holidays retrieved successfully.');
    }

    public function set_year_holidays(Request $request){
        $yearlyvacation = YearlyVacation::updateOrCreate(['year' => $request->vacation['year']],$request->vacation);
        return $this->sendResponse($yearlyvacation, 'yearly vacation retrieved successfully.');
    }

    public function edit_year_holiday(Request $request){
        $holiday = YearlyHoliday::find($request->holiday['id']);
        if(!$holiday){
            return $this->sendError(null, 'something went wrong!.',500);
        }
        $holiday->update($request->holiday);
        return $this->sendResponse($holiday, 'yearly vacation retrieved successfully.');
    }

    public function create_year_holiday(Request $request){
        $holiday = YearlyHoliday::create($request->holiday);
        return $this->sendResponse($holiday, 'yearly vacation retrieved successfully.');
    }


    public function delete_year_holiday(Request $request){
        $year = YearlyHoliday::find($request->id);
        if(!$year){
            return $this->sendError(null, 'something went wrong!.');
        }
        $year->delete();
        return $this->sendResponse($request->id, 'yearly vacation retrieved successfully.');
    }


    public function get_week_vacations(Request $request) {
        $vacations = WeeklyVacation::where('year',$request->year)->pluck('day')->map(function($day) {
                return ucwords($day);
        });
        return $this->sendResponse($vacations, 'weekly vacation retrieved successfully.');
    }


    public function set_week_vacations(Request $request) {
        WeeklyVacation::where('year',$request->year)->delete();
        $vacations = [];

        if(is_array($request->vacations) && !empty($request->vacations)) {
            foreach($request->vacations as $vacation) {
                $vacations[]= [
                    'year' => $request->year,
                    'day'   => $vacation
                ];
            }
        }
        WeeklyVacation::insert($vacations);
        return $this->sendResponse($request->vacations, 'weekly vacation retrieved successfully.');
    }



    public function get_assigned_projects(Request $request) {
        $assigns_projects =  auth()->user()->assigns->map(function($assign){
            return [
                'id' => $assign->id,
                'name' => $assign->name
            ];
        });
        return $this->sendResponse($assigns_projects, 'projects retrieved successfully.');
    }

    public function get_day() {
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $attend = auth()->user()->attendes()->where('date',$date)->first();
        $started= $attend && $attend->from != '' ? true : false;
        $ended = $attend && $attend->to != '' ? true : false;
        return $this->sendResponse(['started' => $started,'ended' => $ended], 'day retrieved successfully.');
    }



    public function set_day_start() {
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $from = $now->format('H:i:s');
        $attend = auth()->user()->attendes()->updateOrCreate(['date' => $date],[
            'from' => $from
        ]);
        return $this->sendResponse($attend, 'day started successfully.');
    }

    public function set_day_end() {
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $to = $now->format('H:i:s');
        $attend = auth()->user()->attendes()->where('date',$date)->first();
        if(!$attend){
            return $this->sendError('Not found',500);
        }

        $attend->update([
            'to' => $to,
            'day_time' => $now->diffInMinutes($attend->from)
        ]);
        return $this->sendResponse('ended', 'projects retrieved successfully.');
    }


    public function set_break_start() {
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $from = $now->format('H:i:s');

        $attend = auth()->user()->attendes()->where('date',$date)->first();
        if(!$attend){
            return $this->sendError('Not found',500);
        }
        $break = $attend->breaks()->whereNull('to')->get();
        if($break->count()){
            return $this->sendError(['message' => 'error there is another breake not closed yet'], 'error there is another breake not closed yet',500);
        }

        $attend->breaks()->create(['from' => $from]);

        return $this->sendResponse('break started', 'projects retrieved successfully.');
    }


    public function set_break_end() {

        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $to = $now->format('H:i:s');

        $attend = auth()->user()->attendes()->where('date',$date)->first();
        if(!$attend){
            return $this->sendError('Not found',500);
        }
        $break = $attend->breaks()->whereNull('to')->first();

        if(!$break->count()){
            return $this->sendError(['message' => 'all breaks is closed'], 'all breaks is closed',500);
        }

        $break->update([
            'to' => $to,
            'break_time'   =>  $now->diffInMinutes($break->from)
        ]);

        return $this->sendResponse('break ended', 'projects retrieved successfully.');

    }

    function get_break_status(Request $request) {

        $status = false;
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $to = $now->format('H:i:s');

        $attend = auth()->user()->attendes()->where('date',$date)->first();

        if($request->mounted && !$attend) {
            return $this->sendResponse(false, 'status retrieved successfully.');
        }
        if(!$attend){
            return $this->sendError('Not found','notfound',500);
        }

        $break = $attend->breaks()->whereNull('to')->first();

        if($break && $break->count()){
            $status = true;
        }



        return $this->sendResponse($status, 'status retrieved successfully.');

    }

    public function project_start(request $request) {
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $from = $now->format('H:i:s');

        $attend = auth()->user()->attendes()->where('date',$date)->first();

        $project = $attend->projects()->where('project_id',$request->pid)->whereNull('to')->first();

        if($project) {
            $project =  [
                'id'      => $project->project_id,
                'name'    => $project->project->name,
                'status'  => true
            ];
            return $this->sendResponse($project, 'status retrieved successfully.');
        }
        $project = $attend->projects()->create(['project_id'=> $request->pid,'from' => $from]);

        $project =  [
                'id'      => $project->project_id,
                'name'    => $project->project->name,
                'status'  => true
            ];


        return $this->sendResponse($project, 'status retrieved successfully.');
    }

    public function project_end(request $request) {
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $to   = $now->format('H:i:s');

        $attend = auth()->user()->attendes()->where('date',$date)->first();
        $project = $attend->projects()->where('project_id',$request->pid)->whereNull('to')->first();

        if(!$project) {
            return $this->sendError(false, 'no projects is runing.',500);
        }

        $project->update([
            'to'=> $to,
            'work_time'   =>  $now->diffInMinutes($project->from)
        ]);

        $project =  [
            'id'      => $project->project_id,
            'name'    => $project->project->name,
            'status'  => false
        ];

        return $this->sendResponse($project, 'status retrieved successfully.');
    }

    public function get_attend_projects(request $request) {
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');

        $attend   = auth()->user()->attendes()->where('date',$date)->first();
        if(!$attend) {
            return $this->sendResponse([], 'Attend projects retrieved successfully.');
        }
        $projects = $attend->projects()->get()->unique('project_id')->map(function($project) use($attend) {
            return [
                'id'      => $project->project_id,
                'name'    => $project->project->name,
                'status'  => $attend->projects()->where('project_id',$project->project_id)->whereNull('to')->first() ? true : false
            ];
        });

        return $this->sendResponse($projects->values(), 'Attend projects retrieved successfully.');
    }


    public function get_day_data(Request $request) {
        $query = auth()->user()->attendes();

        if($request->input('all')) {
            $query = Attend::query();
        }
        $attend = $query->with('projects','projects.project','breaks')->where('date',$request->date)->first();
        return $this->sendResponse($attend, 'Attends retrieved successfully.');

    }



    public function get_working_days(Request $requset){
        $now =  Carbon::now();
        $date = $now->format('Y-m-d');
        $query = auth()->user()->attendes();
        if($requset->input('all')){
            $query = Attend::query();
        }

        // $attends = $query->where('date','!=',$date)->whereActive(true)->get()->map(function($attend) use($requset) {
        //     $arr = [
        //         'title' => $attend->date,
        //         'start' => $attend->date,
        //         'end'   => $attend->date
        //     ];

        //     if($requset->input('all')) {
        //         $arr['title'] = $attend->user ? $attend->user->name : '';
        //     }

        //     return $arr;
        // });


        $attends = $query->whereActive(true)->get()->map(function($attend) use($requset) {
            $arr = [
                'title' => $attend->date,
                'start' => $attend->date,
                'end'   => $attend->date
            ];

            if($requset->input('all')) {
                $arr['title'] = $attend->user ? $attend->user->name : '';
            }

            return $arr;
        });

        return $this->sendResponse($attends, 'Attends retrieved successfully.');
    }


    public function get_employees_attends(Request $request) {

        if(!auth()->user()->isAdmin() && !auth()->user()->can('manage-attendees') && !auth()->user()->can('hr') && !auth()->user()->can('hr-assistant')){
            return $this->sendError('not authorized');
        }
        $id =  $request->input('params')['id'] == "null" ? null : $request->input('params')['id'];

        $user = User::with(['attendes' => function($query) use($request){

            $from = date("Y-m-d",strtotime($request->input('params')['from']));
            $to   = date("Y-m-d",strtotime($request->input('params')['to']));

            if($request->input('params')['from'] || $request->input('params')['to']){

                if($request->input('params')['from'] && !$request->input('params')['to']){
                    $query->where('date','>=',$from);
                }

                if(!$request->input('params')['from'] && $request->input('params')['to']){
                    $query->where('date','<=',$to);
                }


                if($request->input('params')['from'] && $request->input('params')['to']){
                    $query->where('date','<=',$to)->where('date','>=',$from);
                }

                $query->sum('day_time');
            }

        },'attendes.breaks']);

        if($id){

            $user = $user->where('id',$id)->get();


            $user  = $user->map(function($useritem){
                return [
                    'from' => request()->input('params')['from'],
                    'to'   => request()->input('params')['to'],
                    'id' => $useritem->id,
                    'name' => $useritem->name,
                    'day_time_total' => $useritem->attendes->sum(function($attend){
                        return $attend->active ? $attend->day_time : 0;
                    }),
                    'break_time_total' => $useritem->attendes->sum(function($attend){
                        return $attend->active ? $attend->breaks->sum('break_time') : 0;

                    }),
                    'attendes' => $useritem->attendes->map(function($attend){
                            return [
                                'attend' => $attend->id,
                                'date' => $attend->date,
                                'from' => $attend->from,
                                'to'   => $attend->to,
                                'day_time'=> $attend->day_time,
                                'active' => $attend->active,
                                'breaks' => $attend->breaks->map(function($br){
								       return [
										   'id' => $br->id,
								            'to' => $br->to,
                                            'from' => $br->from,
                                            'break_time'=> $br->break_time
										   ];
								}),
                                'breaks_total' => $attend->breaks->sum(function($break){
                                        return $break->break_time;
                                })
                            ];
                    }),
                ];
            });

            if($request->input('params')['send']){
                WorkTimeReportJob::dispatch($user->first());
            }
			return $this->sendResponse(['user' => $user->first()], 'Attends retrieved successfully.');
        }
    }
     public function get_employees_attends_report(Request $request) {
        // if(!auth()->user()->isAdmin() && !auth()->user()->can('manage-attendees') && !auth()->user()->can('hr') && !auth()->user()->can('hr-assistant')){
        //     return $this->sendError('not authorized');
        // }

        if($request->params){
            $request->initialize(collect(json_decode($request->params))->toArray());
            $request->merge(['export_excel'=> true]);
        }else {
            $request->user = json_decode($request->user);
        }


        $attends = Attend::query();

        if(!$request->search){
                if($request->user){
                    $attends = $attends->whereHas('user',function($query) use($request){
                        $query->where('id',$request->user->id);
                    });
                }

                if($request->from){
                    $attends = $attends->where('date','>=',$request->from);
                }

                if($request->to){
                    $attends = $attends->where('date','<=',$request->to);
                }
            }else {
                $attends = $attends->whereHas('user',function($query) use($request){
                    $query->where('name','like','%'.$request->search.'%');
                });
            }

        if($request->export_excel) {
            $attends = $attends->get();
            return Excel::download(new ReportsAttendsExports(json_encode(AttendResource::collection($attends))), date('d-m-Y').'-attend-report.xlsx');

        }
        $attends = $attends->paginate();
        $attends = new AttendCollection($attends);
		return $this->sendResponse(['attends' => $attends], 'Attends retrieved successfully.');
    }






    public function attendees_report_send_email(Request $request){



        Mail::raw($request->message, function($message) use($request) {

            $request->validate([
                'to'  => 'required|email',
                'cc'  => 'nullable|email',
                'bcc' => 'nullable|email',
            ]);
            $image = str_replace('data:image/png;base64,', '', $request->attachement);
            $image = str_replace(' ', '+', $image);
            $message->attachData(base64_decode($image),'Attendees.png', ['mime' => 'image/png']);
            $message->from("noreply@alferp.de");
            $message->to($request->to);

            if($request->cc){
                $message->cc($request->cc);
            }

            if($request->bcc){
                $message->bcc($request->bcc);
            }
            $message->subject($request->subject);
            $message->setBody('<p>'.$request->message.'</p>', 'text/html');
        });


        // Mail::raw('Text', function ($message) use($request) {
        //     $message->to('contact@contact.com');
        // });
        return $this->sendResponse('', 'Message Sent successfully.');
    }

    public function set_employee_attend(Request $request) {
        if(!auth()->user()->isAdmin() && !auth()->user()->can('manage-attendees') && !auth()->user()->can('hr') && !auth()->user()->can('hr-assistant')){
            return $this->sendError('not authorized');
        }
        $attend = Attend::findOrFail($request->params['attend']);
        if(!$attend) {
            return $this->sendError('not found');
        }

        $attend->update($request->params);
        $attend->breaks()->delete();
        $attend->breaks()->createMany($request->params['breaks']);
		return $this->sendResponse(['attend' => $attend], 'Attends retrieved successfully.');
    }

    public function submit_attendees_to_review(Request $request) {

        $attend = auth()->user()->attendes()->find($request->id);
        $request->merge(['active' => 0]);

        if(!$attend) {
            $attend = auth()->user()->attendes()->create($request->all());
            $attend->breaks()->createMany($request->breaks);
        } else {
            $attendTemp = AttendTemp::create($request->merge(['id' => $attend->id,'user_id' => auth()->user()->id])->except('active'));
            $attendTemp->breaks()->createMany($attend->breaks->toArray());

            $attend->update($request->all());
            $attend->breaks()->delete();
            $attend->breaks()->createMany($request->breaks);
        }

        // send notification to admin

        AttendeesReviewJob::dispatch($attend->load('user'));
        return $this->sendResponse([], 'Attends saved successfully.');
    }
    
    public function get_preview_attend(Attend $attend){
        $attend      = $attend->load('breaks');
        $attend_old = AttendTemp::find($attend->id);
        if($attend_old){
            $attend_old->load('breaks');
        }
        return $this->sendResponse(['attend' => $attend,'attend_old' => $attend_old], 'Attends preview retrived successfully.');
    }
    
    public function set_attendees_active(Request $request) {

        $attend = Attend::findOrFail($request->id);
        if(!$attend) {
            return $this->sendError('not found');
        }
        $attend->update(['active' => 1]);


        $attend_old = AttendTemp::find($attend->id);
        if($attend_old){
            $attend_old->breaks()->delete();
            $attend_old->delete();
        }
       return $this->sendResponse([], 'Attends actived successfully.');
    }

    public function set_attendees_rejected(Request $request) {
        $attend_old = AttendTemp::find($request->id)->first();
        if(!$attend_old) {
            return $this->sendError('not found');
        }
        $attend = Attend::find($attend_old->id);
        $attend->update($attend_old->toArray());
        $attend->breaks()->delete();
        $attend->breaks()->createMany($attend_old->breaks->toArray());
        $attend->update(['active' => 1]);
        $attend_old->breaks()->delete();
        $attend_old->delete();
       return $this->sendResponse([], 'Attends actived successfully.');


    }


}
