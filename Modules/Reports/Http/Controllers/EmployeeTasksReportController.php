<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController;
use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;

use Modules\Reports\Http\Resources\ItemResource;
use Modules\Reports\Http\Resources\ItemCollection;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsEmployessTasksExports;

class EmployeeTasksReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $projects = Project::query();
        $tasks    = Task::query();
        $tickets  = Ticket::query();

        $user = json_decode($request->user);
        $dates = explode(',',$request->date);

        if($user) {
            $projects = $projects->wherehas('assigns',function($a) use($user) {
                $a->where('name',$user->name);
            });

            $tasks = $tasks->wherehas('responsible',function($a) use($user) {
                $a->where('name',$user->name);
            });

            $tickets = $tickets->wherehas('assigns',function($a) use($user) {
                $a->where('name',$user->name);
            });
        }

        if($dates) {
            $single = true;
            if(count($dates) > 1) {
                $single = false;
            }

            if(!$single) {
                $projects = $projects->whereBetween('created_at',[$dates[0]." 00:00:00",$dates[1]." 23:59:59"]);
                $tasks    = $tasks->whereBetween('created_at',[$dates[0]." 00:00:00",$dates[1]." 23:59:59"]);
                $tickets  = $tickets->whereBetween('created_at',[$dates[0]." 00:00:00",$dates[1]." 23:59:59"]);
            }else {
                $projects = $projects->where('created_at','like',$dates[0].'%');
                $tasks    = $tasks->where('created_at','like',$dates[0].'%');
                $tickets  = $tickets->where('created_at','like',$dates[0].'%');
            }
        }


        if($request->search && $request->search != 'null') {
            $projects = $projects->where('name','like','%'.$request->search.'%');
            $tasks    = $tasks->where('name','like','%'.$request->search.'%');
            $tickets  = $tickets->where('name','like','%'.$request->search.'%');
        }



        $projects  = $projects->get();
        $tasks     = $tasks->get();
        $tickets   = $tickets->get();

        $allItems = new Collection; //Create empty collection which we know has the merge() method
        $allItems = $allItems->merge($projects);
        $allItems = $allItems->merge($tasks);
        $allItems = $allItems->merge($tickets);
        $sortedItmes = $allItems->sortBy(function($row)
        {
            return $row->created_at;
        });

        if($request->export_excel){
            return Excel::download(new ReportsEmployessTasksExports(json_encode(ItemResource::collection($sortedItmes))), date('d-m-Y').'-employees-tacks.xlsx');
            //pdf return Excel::download(new ReportsEmployessTasksExports(json_encode(ItemResource::collection($sortedItmes))), date('d-m-Y').'-employees-tacks.pdf','Dompdf');
        }


        return $this->sendResponse(new ItemCollection(paginate($sortedItmes)),'Items retrived');
    }
}
