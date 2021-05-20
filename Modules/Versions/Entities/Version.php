<?php

namespace Modules\Versions\Entities;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $fillable = ['version','url'];
}
