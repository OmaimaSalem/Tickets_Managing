<?php

namespace Modules\Activity\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Controllers\API\BaseController;

use Modules\Activity\Entities\Activity;

use Modules\Activity\Http\Resources\ActivityCollection;
use App\Models\User;

class ActivityController extends BaseController
{
    public function addToLog($subject, $client_id = null, $project_id = null, $ticket_id = null, $task_id = null, $receipt_id = null, $offer_id = null)
    {
    	$log = [];

    	$log['subject'] = $subject;
        $log['user_id'] = (!empty(auth()->user()->id)) ? auth()->user()->id : 1 ;
        $log['client_id'] = $client_id;
        $log['project_id'] = $project_id;
        $log['ticket_id'] = $ticket_id;
        $log['task_id'] = $task_id;
        $log['receipt_id'] = $receipt_id;
        $log['offer_id'] = $offer_id;

    	Activity::create($log);
    }

    public function logActivityLists()
    {
    	return Activity::latest()->get();
    }

    public function logActivityListsByClientId(Request $request)
    {
        $user = User::find($request->id);
        if($user->type == 'client'){
            $activities = Activity::with('user', 'project', 'ticket', 'task')
                                ->where('client_id', $request->id)->latest()->paginate();
        }
        else{
            $activities = Activity::with('user', 'project', 'ticket', 'task')
                                ->where('user_id', $request->id)->latest()->paginate();
        }
        return $this->sendResponse(new ActivityCollection($activities), 'Activities log retrieved successfully.');
    }
}
