<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectDiscussionReply extends Model
{
    protected $table = 'project_discussion_replies';

    protected $fillable = ['title', 'content', 'created_by','file','discussion_id'];


    public function discussion(){
        return $this->belongsTo('App\Models\ProjectDiscussion','id','discussion_id');
    }

    public function creator(){
        return $this->hasOne('App\Models\User','id','created_by');
    }

}
