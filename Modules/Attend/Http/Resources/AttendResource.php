<?php

namespace Modules\Attend\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use DateTime;
class AttendResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->user->id,
            'name'         => $this->user->name,
            'date'         => $this->date,
            'entry'        => $this->from,
            'entry_late'   => $this->entry_late(),
            'entry_early'  => $this->entry_early(),
            'exit'         => $this->to,
            'exit_late'    => $this->exit_late(),
            'exit_early'   => $this->exit_early(),
            'break'        => gmdate("H:i:s", mktime(2,$this->breaks->sum('break_time'))),
            'work_time'   =>  gmdate("H:i:s", mktime(2,$this->day_time))
        ];
    }

    public function startTime(){
        return $this->user->attend_data ? $this->user->attend_data->start : '8:30:00';
    }


    public function exitTime(){
        return $this->user->attend_data ? $this->user->attend_data->end : '17:30:00';
    }

    public function entry_late(){
        $a = new DateTime($this->startTime());
        $b = new DateTime($this->from);
        if($a < $b) {
            return $a->diff($b)->format('%H:%I:%S');
        }else {
            return '00:00:00';
        }
    }

    public function entry_early() {
        $a = new DateTime($this->startTime());
        $b = new DateTime($this->from);
        if($a > $b) {
            return $a->diff($b)->format('%H:%I:%S');
        }else {
            return '00:00:00';
        }
    }



    public function exit_late(){
        $a = new DateTime($this->exitTime());
        $b = new DateTime($this->to);
        if($a < $b) {
            return $a->diff($b)->format('%H:%I:%S');
        }else {
            return '00:00:00';
        }
    }

    public function exit_early() {
        $a = new DateTime($this->exitTime());
        $b = new DateTime($this->to);
        if($a > $b) {
            return $a->diff($b)->format('%H:%I:%S');
        }else {
            return '00:00:00';
        }
    }

}
