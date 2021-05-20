<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectDiscussion extends Model
{
    protected $table = 'project_discussions';

    protected $fillable = ['title', 'content', 'created_by','file','project_id'];


    public function project(){
        return $this->hasOne('App\Models\project');
    }

    public function creator(){
        return $this->hasOne('App\Models\User');
    }

    public function responsibles(){
        return $this->belongsToMany('App\Models\User','project_discussion_user', 'project_discussion_id', 'client_id');
    }

    public function replies(){
        return $this->hasMany('App\Models\ProjectDiscussionReply','discussion_id','id')->orderBy('created_at','DESC');
    }
    public function files(){
        return $this->hasMany('App\Models\DiscussionFiles','discussion_id','id');
    }

    public function isManager($assign_to , $project_id){

        if(DB::table('project_assigns')->select("*")
        ->where("project_id", $project_id)->where("assign_to",$assign_to)
        ->exists()){
            return true;
        }else{
            return false;
        }

    }

    public function addReply($id){

        DB::table("project_discussions")->select('comments_num')
        ->where('id',$id)
        ->update(['comments_num' => $this->comments_num + 1]);
    }

    public function returnOldReplies($replyId){
        return  $this->replies->where('reply', 1)->where('id', '!=' , $replyId);
    }


}
