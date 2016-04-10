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
use Walladog\Pet;

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
        Auth::loginUsingId(Authorizer::getResourceOwnerId());

        $validator = Validator::make($request->only(['pet_name','id_pet_race','user_id','partner_id','pet_cross_description','pet_description','birthdate','sterile']), [
            'pet_name' => 'string|max:100|required',
            'id_pet_race' =>  'exists:pet_races,id|required',
            'id_pet_type' => 'exists:pet_types,id|required',
            'user_id' => 'exists:users,id',
            'partner_id' => 'exists:partners,id',
            'pet_cross_description' => 'string|max:255',
            'pet_description' => 'string|max:255',
            'sterile' => '',
            'birthdate' => 'date_format:Y/m/d'

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
        $pet->user_id = $request->get('user_id');
        $pet->partner_id = $request->get('partner_id');
        $pet->pet_cross_description = $request->get('pet_cross_description');
        $pet->pet_description = $request->get('pet_description');
        $pet->sterile = $request->get('sterile');
        $pet->birthdate = $request->get('birthdate');
        $pet->rating = 0;
        $pet->visits = 0;
        $pet->deleted = 0;

        $pet->save();

        return response()->json($pet);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
