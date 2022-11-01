<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Cart extends Model {

    protected $table = 'cart';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'shop_profile_id', 'sub_total', 'shipping_total', 'total'];
    
    
}

