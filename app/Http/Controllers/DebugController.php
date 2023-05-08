<?php

namespace App\Http\Controllers;

use App\Jobs\logDbug;
use App\Models\ProductsImportApi;
use App\Models\testMongo;
use App\Models\WardsImportApi;
use App\Utility\dBug;
use App\Utility\RedisUtility;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DebugController extends Controller
{
    public function install_command_migrate(Request $request)
    {
        $dfg = Artisan::call('migrate',
            array(
                '--path' => 'database/migrations/v2',
                '--force' => true));
        dd($dfg);
    }
    public function install_command(Request $request)
    {
        $time = $request->get("time", false);
        if (empty($request->command)) {
            dd('chưa chuyền param query: command');
        }
        $command = $request->command;
        if ($time) $command .= " " . $time;
        $dfg = Artisan::call($command);
        dd($dfg);
    }
    public function importProduct(Request $request)
    {
        if($request->hasFile('file')){
            $import = new ProductsImportApi;
            Excel::import($import, request()->file('file'));
        }
        return back();
    }
    public function importWard(Request $request)
    {
        if($request->hasFile('file')){
            $import = new WardsImportApi;
            Excel::import($import, request()->file('file'));
        }
//        $country = Country::where('name','Vietnam2')->first();
//        if(!$country){
//            $country= Country::create([
//                'code'=>'VN2',
//                'name'=>'Vietnam2',
//                'status'=>1
//            ]);
//        }
//        dd(234);
        return back();
    }
    public function test(Request $request)
    {
//        $fgdfg = testMongo::orderBy()->paginate(1000);
//        $fgdfg =  DB::connection('mongodb')->collection('database_log')->where('building_id',37)->paginate(10/**/0);
//        dd($fgdfg);
    }
}
