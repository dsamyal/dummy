<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use DB;

class Jobs extends Model {

    protected $table = 'jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'job_title', 'i_am', 'looking_for', 'location', 'from_date_time', 'end_date_time', 'image', 'description', 'payment'
    ];

}
