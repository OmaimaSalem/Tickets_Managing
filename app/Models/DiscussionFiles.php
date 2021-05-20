<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscussionFiles extends Model
{

    protected $table = 'discussion_files';
    public $timestamps = false;
    protected $fillable = array('discussion_id', 'file_path');

    public function discussion(){
        return $this->belongsTo('App\Models\ProjectDiscussion','id','discussion_id');
    }



}
