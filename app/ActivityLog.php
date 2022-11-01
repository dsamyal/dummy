<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model {

    protected $table = 'activity_log';
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'profile_id', 'object_id', 'type', 'message', 'is_repost',
    ];
	
	public function users()
    {
        return $this->belongsTo('App\User', 'user_id', 'id')
            ->withDefault([
                'id' => 0,
                'name' => 'N/A',
                'full_name' => '',
                'tagname' => '',
            ])->select('id','name','full_name','tagname');
    }
}
