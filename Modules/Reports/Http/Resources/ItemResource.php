<?php

namespace Modules\Reports\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ItemResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function getClassType() {
        return explode("\\",get_class($this->resource))[2];
    }
    public function toArray($request)
    {
        $return = [
            'name'          => $this->name,
            'type'          => $this->getClassType(),
            'date_form'     => $this->created_at,
            'date_to'       => $this->due_date,
            'time_consumed' => gmdate("H:i:s",$this->estimated_time),
            'status'        => $this->status ? $this->status->name : null,
        ];

        if(in_array($this->getClassType(),['Project','Ticket'])) {
            $return['client'] =  $this->owner ? $this->owner->map(function($own){
                return [
                    'id' => $own->id,
                    'name' => $own->name,
                ];
            }): null;
        } else {
            $return['client'] =  $this->project ? $this->project->owner->map(function($own){
                return [
                    'id' => $own->id,
                    'name' => $own->name,
                ];
            }): null;

        }

        return $return;
    }
}
