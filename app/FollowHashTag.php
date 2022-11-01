<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowHashTag extends Model {

    protected $table = 'follow_hash_tags';
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash_tag_id', 'user_id'
    ];
    
    public function hashTags()
    {
        return $this->belongsTo('App\HashTag', 'hash_tag_id');
    }

}
