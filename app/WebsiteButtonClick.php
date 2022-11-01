<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WebsiteButtonClick
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $button_id
 * @property integer $product_id
 * @property integer $user_clicked_id
 */

class WebsiteButtonClick extends Model {

    protected $table = 'website_button_clicks';
    /**
     * @var bool
     */
    public $timestamps = true;

}
