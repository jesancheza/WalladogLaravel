<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use Walladog\Address;
use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\Location;
use Walladog\Site;

class SitesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Site::with('user','category','type','comments','address')->where('deleted', 0)->paginate(15));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
        Auth::loginUsingId(Authorizer::getResourceOwnerId());

        $validator = Validator::make($request->only(['site_category_id','site_type_id','pet_type_id','name','description','location','address']), [
            'site_category_id' => 'exists:site_categories,id|required',
            'site_type_id' =>  'exists:site_types,id|required',
            'pet_type_id' => 'exists:pet_types,id|required',
            'name' => 'string|max:255|required',
            'description' => 'string',
            'location.latitude' => 'string|required_with:location',
            'location.longitude' => 'string|required_with:location',
            'address.address1' => 'string|required_with:addreess|max:255',
            'address.address2' => 'string|required_with:addreess|max:255',
            'address.province_txt' => 'string|required_with:addreess|max:255',
            'address.city_txt' => 'string|required_with:addreess|max:255',
            'address.cp_txt' => 'string|required_with:addreess|max:5'
        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $site = new Site();

        $site->name = $request->get('name');
        $site->description = $request->get('description');
        $site->site_category_id = $request->get('site_category_id');
        $site->site_type_id = $request->get('site_type_id');
        $site->pet_type_id = $request->get('pet_type_id');
        $site->user_id = Auth::id();
        $site->deleted = 0;

        $site->save();

        if ($request->get('location')){
            $location1 = Location::create(array('latitude'=>$request->get('location')['latitude'],'longitude'=>$request->get('location')['longitude']));

            $location1->site()->associate($site);
            $location1->save();
        }

        if ($request->get('address')){
            $address = Address::create(array(
                'address1'=>$request->get('address')['address1'],
                'address2'=>$request->get('address')['address2'],
                'province_txt'=>$request->get('address')['province_txt'],
                'city_txt'=>$request->get('address')['city_txt'],
                'cp_txt'=>$request->get('address')['cp_txt']
                ));

            $address->site()->associate($site);
            $address->save();
        }

        return response()->json($site);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $site = Site::with('user','category','type','comments','location')->findOrFail($id);

        if($site->deleted == 1){
            return response()->json([ 'error' => 'Site don\'t exit'], 401);
        }

        return response()->json($site); //Get the resource
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        Auth::loginUsingId(Authorizer::getResourceOwnerId());

        $validator = Validator::make($request->only(['site_category_id','site_type_id','pet_type_id','name','description','location','address']), [
            'site_category_id' => 'exists:site_categories,id|required',
            'site_type_id' =>  'exists:site_types,id|required',
            'pet_type_id' => 'exists:pet_types,id|required',
            'name' => 'string|max:255|required',
            'description' => 'string',
            'location.latitude' => 'string|required_with:location',
            'location.longitude' => 'string|required_with:location',
            'address.address1' => 'string|required_with:addreess|max:255',
            'address.address2' => 'string|required_with:addreess|max:255',
            'address.province_txt' => 'string|required_with:addreess|max:255',
            'address.city_txt' => 'string|required_with:addreess|max:255',
            'address.cp_txt' => 'string|required_with:addreess|max:5'
        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $site = Site::with('location')->findOrfail($id);

        if(Gate::denies('update',$site)) {
            if ($site->deleted == 1){
                return response()->json([ 'error' => 'Site don\'t exit'], 401);
            }else{
                return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
            }
        }

        $site->name = $request->get('name');
        $site->description = $request->get('description');
        $site->site_category_id = $request->get('site_category_id');
        $site->site_type_id = $request->get('site_type_id');
        $site->pet_type_id = $request->get('pet_type_id');

        if ($request->get('location')){
            $site->location->latitude = $request->get('location')['latitude'];
            $site->location->longitude = $request->get('location')['longitude'];
        }

        if ($request->get('address')){

            $site->address->address1 = $request->get('address')['address1'];
            $site->address->address2 = $request->get('address')['address2'];
            $site->address->province_txt = $request->get('address')['province_txt'];
            $site->address->city_txt = $request->get('address')['city_txt'];
            $site->address->cp_txt = $request->get('address')['cp_txt'];

        }

        $site->push();

        return response()->json($site);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Auth::loginUsingId(Authorizer::getResourceOwnerId());

        $site = Site::with('user')->findOrFail($id); //Get the resource

        if(Gate::denies('destroy',$site)) {
            return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
        }

        $site->deleted = 1;
        $site->save();

        return response()->json($site);
    }
}
