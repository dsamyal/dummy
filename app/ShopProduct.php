<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopProduct extends Model {

    protected $table = 'shop_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'shop_profile_id', 'name', 'artist_name', 'description', 'tags', 'type_id', 'type', 'category_type', 'currency', 'price', 'package_size', 'package_weight', 'filter_id', 'quantity', 'comment', 'sale', 'status', 'contact_email', 'website_link','approval_date','shipping_included',
    ]; 
    
    function shop_profiles()
    {
        return $this->belongsTo('App\ShopProfile', 'shop_profile_id');
    }
    
    function shop_product_details()
    {
        return $this->hasMany('App\ShopProductDetail');
    }
    
    function shop_product_files()
    {
        return $this->hasMany('App\ShopProductFile');
    }

    function shop_product_meta()
    {
        return $this->hasMany('App\ShopProductMeta', 'shop_product_id');
    }

    function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function scopeByApproveStatus($query)
    {
        return $query->where([
            [$this->table . '.status', true],
            [$this->table . '.approval_date', '<=', Carbon::now()]
        ]);
    }

    public function approveCode()
    {
        return $this->hasOne(ProductApproveCode::class);
    }

    public function getIsApprovedAttribute()
    {
        if ($this->status == true && $this->approval_date <= Carbon::now()) {
            return true;
        }

        return false;
    }

    public static function createApprovalCode($productId)
    {
        $code = new ProductApproveCode();
        $code->shop_product_id = $productId;
        $code->approve_code = Str::random(120);
        $code->save();

        return $code->approve_code;
    }
}

