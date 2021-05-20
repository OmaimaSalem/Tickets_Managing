<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Exports\ReportsUsersTimeExports;
use Maatwebsite\Excel\Facades\Excel;

class TimeTrackingReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $month = request()->month && trim(request()->month) != '' && request()->month != 'null' ? date('m',strtotime(request()->month)) : date('m');
        $search = request()->search && trim(request()->search) != '' && request()->search != 'null' ? request()->search : null;
        $users = User::with(['ticket_tracking' => function($ticket_tracking) use($month){
            $ticket_tracking->with('ticket.project')->whereMonth('created_at',$month);
        },'task_tracking' => function($task_tracking) use($month){
            $task_tracking->with('task.project')->whereMonth('created_at',$month);
        }]);

        if($search) {
            $users->where('name','like','%'.$search.'%');
        }


        if(request()->export_excel){
            $users = $users->where("type","regular-user")->get();
            return Excel::download(new ReportsUsersTimeExports(json_encode(UserResource::collection($users))), date('d-m-Y').'-users-times.xlsx');
        }


        $users = $users->where("type","regular-user")->paginate();

        $users = new UserCollection($users);
        return $this->sendResponse($users,'Users retrived');
    }


}
