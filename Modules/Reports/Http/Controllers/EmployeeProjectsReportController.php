<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use App\Http\Resources\User\UserCollection;
use App\Exports\ReportsUsersExports;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeProjectsReportController extends BaseController
{

    public function export(Request $request) {
        $users  = User::query();
        $query = json_decode($request->filter);
        $relation = 'r_tickets';
        if($query->search && !$request->global_search) {

            switch($query->type)
            {
                case 'ticket_name':
                    $relation = 'r_tickets';
                break;
                case 'task_name':
                    $relation = 'r_tasks';
                break;
                case 'project_name':
                    $relation = 'projects_assigns_to';
                break;
                default:
                    $relation = 'r_tickets';
                break;
            }


            $users = $users->whereHas($relation , function($q) use($request){
                $q->where('name','like','%'.$request->search.'%');
            });

            // $users = $users->whereHas($relation , function($q) use($request,$relation){
            //     $q->where(function($sq) use($request,$relation){
            //            $sq->orwhere('name','like','%'.$request->search.'%');
            //     });

            // });

        }


      if($query->global_search && $query->global_search != "" && $query->global_search != "null") {

            $users  = User::query();
            $users = $users->where('name', 'LIKE', '%' . $query->global_search . '%');
            $users = $users->orWhere('email', 'LIKE', '%' . $query->global_search . '%');
            $users = $users->orwhereHas('metadata',function($q) use($query){
                $q->where('position', 'LIKE', '%' . $query->global_search . '%');
                $q->orWhere('mobile', 'LIKE', '%' . $query->global_search . '%');
                $q->orWhere('company', 'LIKE', '%' . $query->global_search . '%');
            });
        }


        $users = $users->where("type","regular-user")->latest()->get();
        return Excel::download(new ReportsUsersExports(new UserCollection($users)), date('d-m-Y').'-project-users.xlsx');
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $users  = User::query();
        $relation = 'r_tickets';
        if($request->search && !$request->global_search) {
            switch($request->type)
            {
                case 'ticket_name':
                    $relation = 'r_tickets';
                break;
                case 'task_name':
                    $relation = 'r_tasks';
                break;
                case 'project_name':
                    $relation = 'projects_assigns_to';
                break;
                default:
                    $relation = 'r_tickets';
                break;
            }
            // $users->whereHas($relation , function($q) use($request,$relation){
            //     $q->where('name','like','%'.$request->search.'%');
            // });

            $users = $users->whereHas($relation , function($q) use($request){
                $q->where('name','like','%'.$request->search.'%');
            });

        }


        if($request->global_search && $request->global_search != "") {
            $users  = User::query();
            $users = $users->where('name', 'LIKE', '%' . $request->global_search . '%');
            $users = $users->orWhere('email', 'LIKE', '%' . $request->global_search . '%');
            $users = $users->orwhereHas('metadata',function($q) use($request){
                $q->where('position', 'LIKE', '%' . $request->global_search . '%');
                $q->orWhere('mobile', 'LIKE', '%' . $request->global_search . '%');
                $q->orWhere('company', 'LIKE', '%' . $request->global_search . '%');
            });
        }


        //   \DB::enableQueryLog();
        $users = $users->where("type","regular-user")->latest()->paginate();
        //  dd(\DB::getQueryLog());


        if($request->excel_export){
            return Excel::download(new ReportsUsersExports($users), date('d-m-Y').'-users.xlsx');
        }

        return $this->sendResponse(new UserCollection($users),'Users retrived');
    }

}
