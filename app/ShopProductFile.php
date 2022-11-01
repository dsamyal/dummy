<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopProductFile extends Model {

    protected $table = 'shop_product_files';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'shop_profile_id', 'shop_product_id', 'file_name', 'type', 'status',
    ];

}

