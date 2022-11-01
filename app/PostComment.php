<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes; 

class PostComment extends Model {

    protected $table = 'post_comment';
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'post_id', 'comment_text', 'user_tags'
    ];

}
