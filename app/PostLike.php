<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostLike extends Model {

    protected $table = 'post_likes';
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'post_id', 'type'
    ];

    public function get_user_is_like($snUserId, $snPostId) {
        $saLike = PostLike::where('user_id', $snUserId)->where([['post_id', $snPostId], ['type', 'like']])->first();
        if(empty($saLike)){
            return 2;
        }else{
            return 1;
        }
    }

}
