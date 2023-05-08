<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campain extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $seachable = ['email', 'app', 'sms'];

    public static function updateOrCreateCampain($title = null, $type, $typeId = null, $total, $status = 0, $sort = 0, $id = null)
    {
        $rs = null;
        $status = ["email" => 0, "app" => 0, "sms" => 0];
        if ($id != null) {
            $rs = self::where('id', $id)->first();
            $rs->title = $title;
            $rs->type = $type;
            $rs->type_id = $typeId;
            $rs->total = json_encode($total);
            $rs->save();
        } else {
            $rs = self::create([
                'title' => $title,
                'type' => $type,
                'type_id' => $typeId,
                'total' => json_encode($total),
                'status' => json_encode($status)
            ]);
        }

        return $rs;
    }

    public static function updateStatus($id, $type ,$value = null)
    {
        $campains = Campain::find($id);
        if($campains){
            $status  = json_decode($campains->status);
            foreach ($status as $key => $value) {
                if($key == $type && $value == null){
                    $status->$key = 1;
                }
            }
            $campains->status = json_encode($status);
            $campains->save();
            return json_encode($status);
        }
        return false;
    }
    public static function updateTotal($id, $type ,$value = null)
    {
        $campains = Campain::find($id);
        if($campains){
            $total  = json_decode($campains->total);
            foreach ($total as $key => $value) {
                if($key == $type && $value == null){
                    $total->$key = 1;
                }else{
                    $total->$key = 0;
                }
            }
            $campains->total = json_encode($total);
            $campains->save();
            return true;
        }
        return false;
    }
    public static function findByType($type)
    {
        return Campain::where(function ($q) use ($type) {
            $q->where('status->'.$type, 0);
            $q->where('total->'.$type,'<>',0);
        })->where('run', 0)->get();
    }
    public static function findByTypeFirst($type)
    {
        return Campain::where(function ($q) use ($type) {
            $q->where('status->'.$type, 0);
            $q->where('total->'.$type,'<>',0);
        })->where('run', 0)->first();
    }
}
