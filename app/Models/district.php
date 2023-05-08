<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class district extends Model
{
    protected $guarded = [];

    public static function getDetail($id)
    {
        return Cache::remember('getDetailDistrictId_'.$id, 86400, function () use($id) {
            return self::find($id);
        });
    }
}
