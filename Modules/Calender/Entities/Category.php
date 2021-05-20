<?php

namespace Modules\Calender\Entities;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['title'];
    protected $table = "calendar_categories";
}
