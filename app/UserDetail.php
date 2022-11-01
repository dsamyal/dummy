<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use DB;

class UserDetail extends Model {

    protected $table = 'user_detail';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'filter', 'category', 'private_email', 'private_description', 'privete_profile_url', 'private_url1', 'private_url2', 'private_url3', 'profile_image_url', 'description', 'image_url1', 'image_url2', 'image_url3', 'email', 'private_name', 'public_name'
    ];

}
