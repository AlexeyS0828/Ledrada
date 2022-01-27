<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $hidden = ['id', 'video_path', 'created_at', 'updated_at'];
}
