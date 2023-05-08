<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{

//    protected $guarded = ['choice_attributes'];
    protected $guarded = [];

    protected $with = ['product_translations', 'taxes'];

    public function getTranslation($field = '', $lang = false)
    {
        $_product = $this->product_translations;
        $lang = $lang == false ? App::getLocale() : $lang;
        $product_translations = Cache::remember('product_translation_name_'.$lang, 86400, function () use($_product,$lang) {
            return $_product->where('lang', $lang)->first();
        });
        return $product_translations != null ? $product_translations->$field : $this->$field;
    }

    public function product_translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function flash_deal_product()
    {
        return $this->hasOne(FlashDealProduct::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionProductBid::class);
    }

    public function scopePhysical($query)
    {
        return $query->where('digital', 0);
    }

    public function scopeDigital($query)
    {
        return $query->where('digital', 1);
    }
}
