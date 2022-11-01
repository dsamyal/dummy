<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Post extends Model {

    use SoftDeletes; 
    protected $table = 'post';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'image', 'owner_name', 'decription', 'tag', 'filter', 'type','video_thumbnail','video_thumb_title'
    ];

}
