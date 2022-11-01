<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Class UserWebsiteButtonClick
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $website_button_id
 * @property integer $quantity
 */

class UserWebsiteButtonClick extends Model {

    protected $table = 'user_website_button_clicks';
    /**
     * @var bool
     */
    public $timestamps = false;

}