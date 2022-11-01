<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Class ShopProductWebsiteButtonClick
 *
 * @property integer $id
 * @property integer $shop_product_id
 * @property integer $website_button_id
 * @property integer $quantity
 */

class ShopProductWebsiteButtonClick extends Model {

    protected $table = 'shop_product_website_button_clicks';
    /**
     * @var bool
     */
    public $timestamps = false;

}