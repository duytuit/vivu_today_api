<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class testMongo extends Model
{
    protected $connection = 'mongodb';

    protected $table = "database_log";

    protected $guarded = [];
}
