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

class AddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Address::with('partner','user','site')->where('deleted', 0)->paginate(15));
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

        $validator = Validator::make($request->only(['address1','address2','province_txt','city_txt','cp_txt']), [
            'address1' => 'string|required|max:255',
            'address2' => 'string|required|max:255',
            'province_txt' => 'string|required|max:255',
            'city_txt' => 'string|required|max:255',
            'cp_txt' => 'string|required|max:5'
        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $address = new Address();

        $address->address1 = $request->get('address1');
        $address->address2 = $request->get('address2');
        $address->province_txt = $request->get('province_txt');
        $address->city_txt = $request->get('city_txt');
        $address->cp_txt = $request->get('cp_txt');

        $address->save();

        return response()->json($address);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $address = Address::with('user','site','partner')->findOrFail($id);

        if($address->deleted == 1){
            return response()->json([ 'error' => 'Address don\'t exit'], 401);
        }

        return response()->json($address); //Get the resource);
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

        $validator = Validator::make($request->only(['address1','address2','province_txt','city_txt','cp_txt']), [
            'address1' => 'string|required|max:255',
            'address2' => 'string|required|max:255',
            'province_txt' => 'string|required|max:255',
            'city_txt' => 'string|required|max:255',
            'cp_txt' => 'string|required|max:5'
        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $address = Address::with('site','user','partner')->findOrfail($id);

        if(Gate::denies('update',$address)) {
            if ($address->deleted == 1){
                return response()->json([ 'error' => 'Address don\'t exit'], 401);
            }else{
                return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
            }
        }

        $address->address1 = $request->get('address1');
        $address->address2 = $request->get('address2');
        $address->province_txt = $request->get('province_txt');
        $address->city_txt = $request->get('city_txt');
        $address->cp_txt = $request->get('cp_txt');

        $address->push();

        return response()->json($address);
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

        $address = Address::with('user','partner','site')->findOrFail($id); //Get the resource

        if(Gate::denies('destroy',$address)) {
            return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
        }

        $address->deleted = 1;
        $address->save();

        return response()->json($address);
    }
}
