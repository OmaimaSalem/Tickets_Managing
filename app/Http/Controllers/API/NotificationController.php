<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;

class NotificationController extends BaseController
{

    public function latest_notification(){
        return $this->sendResponse(['notifications' => auth()->user()->notifications()->latest()->take(10)->get(),'count' => auth()->user()->unreadNotifications()->count()],'Menu notification retrived');
    }

    public function all_notifications(){
        return $this->sendResponse(['notifications' => auth()->user()->notifications()->latest()->paginate(15),'count' => auth()->user()->unreadNotifications()->count()],'All notification retrived');
    }




    public function latest_layout_notification(){
        return $this->sendResponse(auth()->user()->unreadNotifications()
        ->where('type', 'Modules\Calender\Notifications\AddEventNotification')
        ->count(),'Layout notification retrived');
    }

    public function reset_layout_notification() {
        return $this->sendResponse(auth()->user()->notifications()
        // ->where('type', 'Modules\Calender\Notifications\AddEventNotification')
        ->get(),'Layout reseted');
    }


    public function read_notification(Request $request) {
        auth()->user()->notifications()->where('id',$request->id)->get()->markAsRead();
        return $this->sendResponse(['id' => $request->id , 'count' => auth()->user()->unreadNotifications()
        // ->where('type', 'Modules\Calender\Notifications\AddEventNotification')
        ->count(),"read_at"=>now()],'notification readed');
    }

    public function read_all_notifications() {
        auth()->user()->notifications()->get()->map(function($noti) {
            $noti->markAsRead();
        });
        return $this->sendResponse([],'All notification marked as read');
    }

    
}
