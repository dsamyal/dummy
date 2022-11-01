<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followers extends Model {

    protected $table = 'followers';
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'follewers_id', 'user_id'
    ];
	
	protected $appends = ['following_user_id']; 
	
	public function getFollowingUserIdAttribute(){
		if(!empty($this->user_id)){
			return $this->user_id; 
		}else{
			return "";
		}
    }
}
