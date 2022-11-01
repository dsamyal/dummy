<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements MustVerifyEmail
{
     protected $connection = 'mysql';

     use SoftDeletes;
     use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'tagname'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	public function userdetail() {
        return $this->hasOne('App\UserDetail')->select('user_id','profile_image_url');
    }

    public function shopProfile()
    {
        return $this->hasOne(ShopProfile::class);
    }

    public function urls()
    {
        return $this->hasMany(UserUrls::class);
    }

    public function shopUrls()
    {
        return $this->urls()->where('type', 'shop');
    }
    public function getIsAdminAttribute()
    {
        return true;
    }
}
