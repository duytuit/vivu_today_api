<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Address extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }
    public static function getDetail($id)
    {
        return Cache::remember('getDetailAddressId_'.$id, 86400, function () use($id) {
            return self::find($id);
        });
    }
}
