<?php

namespace Modules\Reports\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Attend\Entities\Vacation;
use App\Http\Controllers\API\BaseController;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsVacationsExports;

class VacationsReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $vacations = Vacation::query();


        $vacations = $vacations->with('user');


        if($request->search && $request->search != 'null') {
            $vacations->whereHas('user',function($q){
                $q->where('name','like','%'.request()->search.'%');
            });
        }

        if($request->export_excel){
            $vacations = $vacations->get();
            return Excel::download(new ReportsVacationsExports($vacations), date('d-m-Y').'-vacations.xlsx');
        }


        $vacations = $vacations->paginate();
        return $this->sendResponse($vacations,'Vacations retrived');

    }


}
