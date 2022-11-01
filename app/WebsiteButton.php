<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WebsiteButton
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 */

class WebsiteButton extends Model {

    protected $table = 'website_buttons';

}
