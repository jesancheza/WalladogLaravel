<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\Image;
use Walladog\Location;
use Walladog\Pet;
use Walladog\User;

class PetsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Pet::with('location','user')->where('deleted', 0)->paginate(15));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
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

        $validator = Validator::make($request->only(['pet_name','id_pet_race','id_pet_type','pet_cross_description','pet_description','birthdate','sterile','hidden_location','hidden_location_city','is_partner','location','images']), [
            'pet_name' => 'string|max:100|required',
            'id_pet_race' =>  'exists:pet_races,id|required',
            'id_pet_type' => 'exists:pet_types,id|required',
            'pet_cross_description' => 'string|max:255',
            'pet_description' => 'string|max:255',
            'sterile' => 'boolean',
            'birthdate' => 'date_format:Y/m/d',
            'hidden_location' => 'boolean',
            'hidden_location_city' => 'string',
            'is_partner' => 'boolean',
            'location.latitude' => 'string|required_with:location',
            'location.longitude' => 'string|required_with:location',
            'images.name' => 'string|required_with:image',
            'images.url_short' => 'url|required_with:image',
            'images.url_big' => 'url|required_with:image'

        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $pet = new Pet();

        $pet->pet_name = $request->get('pet_name');
        $pet->id_pet_race = $request->get('id_pet_race');
        $pet->id_pet_type = $request->get('id_pet_type');
        $pet->user_id = Auth::id();
        if ($request->get('is_partner') !== null && $request->get('is_partner')){
            $user = User::with('partner')->findOrFail(Auth::id());
            if ($user->partner_id != 0){
                $pet->partner_id = $user->partner_id;
            }
        }
        $pet->pet_cross_description = $request->get('pet_cross_description');
        $pet->pet_description = $request->get('pet_description');
        $pet->sterile = $request->get('sterile');
        $pet->birthdate = $request->get('birthdate');
        $pet->hidden_location = $request->get('hidden_location');
        $pet->hidden_location_city = $request->get('hidden_location_city') !== null ? $request->get('hidden_location_city') : "Sin ubicación";
        $pet->rating = 0;
        $pet->visits = 0;
        $pet->deleted = 0;


        $pet->save();

        if ($request->get('location')){
            $location1 = Location::create(array('latitude'=>$request->get('location')['latitude'],'longitude'=>$request->get('location')['longitude']));

            $location1->pet()->associate($pet);
            $location1->save();
        }

        if ($request->get('images')){
            foreach ($request->get('images') as $imageJson){
                $image = Image::create(array('name'=>$imageJson['name'],'url_short'=>$imageJson['url_short'],'url_big'=>$imageJson['url_big']));

                $image->pet()->associate($pet);
                $image->save();
            }
        }


        return response()->json($pet);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pet = Pet::with('location','images','partner')->findOrFail($id);

        if($pet->deleted == 1){
            return response()->json([ 'error' => 'Pet don\'t exit'], 401);
        }

        return response()->json($pet); //Get the resource);
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

        $validator = Validator::make($request->only(['pet_name','id_pet_race','id_pet_type','pet_cross_description','pet_description','birthdate','sterile','hidden_location','hidden_location_city','is_partner','location','image']), [
            'pet_name' => 'string|max:100|required',
            'id_pet_race' =>  'exists:pet_races,id|required',
            'id_pet_type' => 'exists:pet_types,id|required',
            'pet_cross_description' => 'string|max:255',
            'pet_description' => 'string|max:255',
            'sterile' => 'boolean',
            'birthdate' => 'date_format:Y/m/d',
            'hidden_location' => 'boolean',
            'hidden_location_city' => 'string',
            'is_partner' => 'boolean',
            'location.latitude' => 'string|required_with:location',
            'location.longitude' => 'string|required_with:location',
            'image.name' => 'string|required_with:image',
            'image.url_short' => 'url|required_with:image',
            'image.url_big' => 'url|required_with:image'

        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $pet = new Pet();

        $pet->pet_name = $request->get('pet_name');
        $pet->id_pet_race = $request->get('id_pet_race');
        $pet->id_pet_type = $request->get('id_pet_type');
        if ($request->get('is_partner') !== null && $request->get('is_partner')){
            $user = User::with('partner')->findOrFail(Auth::id());
            if ($user->partner_id != 0){
                $pet->partner_id = $user->partner_id;
            }
        }
        $pet->pet_cross_description = $request->get('pet_cross_description');
        $pet->pet_description = $request->get('pet_description');
        $pet->sterile = $request->get('sterile');
        $pet->birthdate = $request->get('birthdate');
        $pet->hidden_location = $request->get('hidden_location');
        $pet->hidden_location_city = $request->get('hidden_location_city') !== null ? $request->get('hidden_location_city') : "Sin ubicación";

        if ($request->get('location')){
            $pet->location->latitude = $request->get('location')['latitude'];
            $pet->location->longitude = $request->get('location')['longitude'];
        }

        $pet->push();

        return response()->json($pet);
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

        $pet = Pet::with('user','partner')->findOrFail($id); //Get the resource
        
        if(Gate::denies('destroy',$pet)) {
            return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
        }
        
        $pet->deleted = 1;
        $pet->save();

        return response()->json($pet);
    }
}
