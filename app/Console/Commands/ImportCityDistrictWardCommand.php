<?php

namespace App\Console\Commands;

use App\Models\Campain;
use App\Models\CampainDetail;
use App\Models\City;
use App\Models\CityTranslation;
use App\Models\Country;
use App\Models\district;
use App\Models\LogImport;
use App\Models\State;
use App\Models\Ward;
use App\Services\SendEmailService;
use App\Utility\RedisUtility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ImportCityDistrictWardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time_start = microtime(true);

        do {
            $get_detail = null;
            try {
                $details = RedisUtility::queuePop(['Redis_Import_City_District_Ward']);
                if ($details == null) {
                    break;
                }
                $get_detail = $details;
                $details = json_decode($details);
                $country = Country::where('name','Vietnam')->first();
                if(!$country){
                    $country= Country::create([
                        'code'=>'VN',
                        'name'=>'Vietnam',
                        'status'=>1
                    ]);
                }
                $state = State::where('name','Việt Nam')->first();
                if(!$state){
                    $state= State::create([
                        'name'=>'Việt Nam',
                        'country_id'=>$country->id,
                        'status'=>1
                    ]);
                }
                $city = City::where('name','like','%'.trim($details->tinh_thanh_pho).'%')->first();
                if(!$city){
                    $city=  City::create([
                        'name'=>trim($details->tinh_thanh_pho),
                        'state_id'=>$state->id,
                        'status'=>1
                    ]);
                    CityTranslation::create([
                        'city_id' => $city->id,
                        'name' => 'Việt nam',
                        'lang' => 'vn'
                    ]);
                }
                $district = district::where('name','like','%'.trim($details->quan_huyen).'%')->first();
                if(!$district){
                    $district= district::create([
                        'name'=>trim($details->quan_huyen),
                        'city_id'=>$city->id
                    ]);
                }
                echo json_encode($district);
                $check_ward = Ward::where('name','like','%'.trim($details->phuong_xa).'%')->first();
                if($check_ward){
                    continue;
                }
                Ward::create([
                    'name'=>trim($details->phuong_xa),
                    'address'=>trim($details->phuong_xa).'-'.trim($details->quan_huyen).'-'.trim($details->tinh_thanh_pho),
                    'district_id'=>$district->id,
                    'city_id'=>$city->id,
                    'country_id'=>$country->id,
                ]);
                $time_end = microtime(true);
                $time = $time_end - $time_start;
            } catch (\Exception $e) {
                LogImport::create([
                    'type' => 1, // import ward district
                    'status' => 0,
                    'data' => $get_detail,
                    'messages' => $e->getLine().'||'.$e->getTraceAsString()
                ]);
            }
        } while ($details != null || $time < 55);

        return true;
    }
}
