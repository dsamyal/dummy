<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model {

    protected $table = 'cart_products';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id', 'seller_id', 'shop_product_id', 'qty', 'type', 'size', 'weight', 'price'];
    
}

