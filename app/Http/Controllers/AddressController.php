<?php

namespace App\Http\Controllers;

use App\Models\district;
use App\Models\Ward;
use App\Utility\dBug;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\City;
use App\Models\State;
use Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $address = new Address;
        if($request->has('customer_id')){
            $address->user_id   = $request->customer_id;
        }
        else{
            $address->user_id   = Auth::user()->id;
        }
        $address->address       = $request->address;
        $address->country_id    = $request->country_id;
        $address->state_id      = $request->state_id??0;
        $address->city_id       = $request->city_id??0;
        $address->ward_id       = $request->ward_id;
        $address->longitude     = $request->longitude;
        $address->latitude      = $request->latitude;
        $address->postal_code   = $request->postal_code;
        $address->phone         = $request->phone;
        $address->save();

        flash(translate('Address info Stored successfully'))->success();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['address_data'] = Address::findOrFail($id);
        $data['states'] = State::where('status', 1)->where('country_id', $data['address_data']->country_id)->get();
        $data['cities'] = City::where('status', 1)->where('state_id', $data['address_data']->state_id)->get();
        $data['ward'] = Ward::find($data['address_data']->ward_id);
        $returnHTML = view('frontend.partials.address_edit_modal', $data)->render();
        return response()->json(array('data' => $data, 'html'=>$returnHTML));
//        return ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        $address->address       = $request->address;
        $address->country_id    = $request->country_id??$address->country_id;
        $address->state_id      = $request->state_id??$address->state_id;
        $address->city_id       = $request->city_id??$address->city_id;
        $address->ward_id       = $request->ward_id??$address->ward_id;
        $address->longitude     = $request->longitude;
        $address->latitude      = $request->latitude;
        $address->postal_code   = $request->postal_code;
        $address->phone         = $request->phone;

        $address->save();

        flash(translate('Address info updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if(!$address->set_default){
            $address->delete();
            return back();
        }
        flash(translate('Default address can not be deleted'))->warning();
        return back();
    }

    public function getStates(Request $request) {
        $states = State::where('status', 1)->where('country_id', $request->country_id)->get();
        $html = '<option value="">'.translate("Select State").'</option>';

        foreach ($states as $state) {
            $html .= '<option value="' . $state->id . '">' . $state->name . '</option>';
        }

        echo json_encode($html);
    }

    public function getCities(Request $request) {
        $cities = City::where('status', 1)->where('state_id', $request->state_id)->get();
        $html = '<option value="">'.translate("Select City").'</option>';

        foreach ($cities as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->name . '</option>';
        }

        echo json_encode($html);
    }
    public function getWards(Request $request) {
        $wards = Ward::select(['id','address'])->get();
        $html = '<option value="">'.translate("Select Ward").'</option>';

        foreach ($wards as $row) {
            $district = district::getDetail($row->district_id);
            $city = City::getDetail($district->city_id);
            $html .= '<option value="' . $row->id . '">' . $row->name .'-'.$district->name.'-'.$city->name. '</option>';
        }

        echo json_encode($html);
    }

    public function set_default($id){
        foreach (Auth::user()->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }
    public function ajaxWards(Request $request)
    {
        if ($request->search) {
            $where[] = ['address', 'like', '%' . $request->search . '%'];
            $wards = $this->getListWards(['where' => $where]);
            $html = '';

            foreach ($wards as $row) {
                $html .= '<option value="' . $row->id . '">' . $row->address . '</option>';
            }
            return response()->json($html);
        }
        $wards = $this->getListWards(['select' => ['id', 'address']]);
        $html = '';

        foreach ($wards as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->address . '</option>';
        }
        return response()->json($html);
    }
   protected function getListWards(array $options = []){
       $default = [
           'select'   => '*',
           'where'    => [],
           'order_by' => 'id DESC',
           'per_page' => 20,
       ];
       $options = array_merge($default, $options);
       extract($options);
       $model = Ward::select($options['select']);
       if ($options['where']) {
           $model = $model->where($options['where']);
       }
       return $model->orderByRaw($options['order_by'])->paginate($options['per_page']);
   }
}
