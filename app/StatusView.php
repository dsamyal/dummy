<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusView extends Model
{
    protected $table = 'status_views';
    use SoftDeletes;
}
