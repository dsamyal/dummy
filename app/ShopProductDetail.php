<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopProductDetail extends Model {

    protected $table = 'shop_product_details';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'shop_profile_id', 'shop_product_id', 'title', 'value'];

}

