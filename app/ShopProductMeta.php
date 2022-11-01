<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ShopProductMeta extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'shop_product_id', 'shop_profile_id', 'type', 'comment', 'user_tags'];
}
