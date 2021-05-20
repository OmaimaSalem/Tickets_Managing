<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\API\BaseController;
use App\Models\Project;
use App\Http\Resources\Project\ProjectCollection;
use App\Http\Resources\Project\ProjectResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsFinancialExports;

class FinancialReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $projects = Project::query();
        $projects = $projects->with('tickets.tracking','tasks.tracking_history');

        if($request->search && $request->search != 'null') {
            $projects->where('name','like','%'.$request->search.'%');
        }

        if($request->export_excel){
            $projects = $projects->get();
            return Excel::download(new ReportsFinancialExports(json_encode(ProjectResource::collection($projects))), date('d-m-Y').'-project-financial.xlsx');
        }

        $projects = $projects->paginate();
        return $this->sendResponse(new ProjectCollection($projects),'Projects retrived');
    }


}
