<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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

        $address = Address::with('user','partner','site')->findOrFail($id); //Get the resource

        if(Gate::denies('destroy',$address)) {
            return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
        }

        $address->deleted = 1;
        $address->save();

        return response()->json($address);
    }
}
