<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopProfile extends Model {
    
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'shop_image_url', 'shop_description', 'company_name', 'name', 'shop_name', 'address_1', 'address_2', 'zip', 'city', 'country', 'phone_1', 'phone_2', 'email', 'rating', 'card_name', 'card_number', 'expiry_month', 'expiry_year', 'security_code', 'shipping_type'
    ];
    
}

