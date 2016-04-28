<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\Location;
use Walladog\Publication;
use Walladog\Site;

class PublicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Publication::with('user','category','type','status')->where('deleted', 0)->paginate(15));
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

        $validator = Validator::make($request->only(['publication_type_id','publication_category_id','short_title','title','short_text','text','date_publish','location']), [
            'publication_type_id' => 'exists:publication_types,id|required',
            'publication_category_id' =>  'exists:publication_categories,id|required',
            'short_title' => 'string|max:255',
            'title' => 'string|max:255|required',
            'short_text' => 'string|max:255',
            'text' => 'string',
            'date_publish' => 'date_format:Y/m/d'

        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        // Publication
        $publication = new Publication();

        $publication->publication_type_id = $request->get('publication_type_id');
        $publication->publication_category_id = $request->get('publication_category_id');
        $publication->short_title = $request->get('short_title');
        $publication->title = $request->get('title');
        $publication->short_text = $request->get('short_text');
        $publication->text = $request->get('text');
        $publication->date_publish = $request->get('date_publish');
        $publication->user_id = Auth::id();
        $publication->publication_status_id = 1;
        $publication->deleted = 0;

        //dd($request->get('location')['latitude']);

        $location = Location::create(array('longitude'=>$request->get('location')['longitude'],'latitude'=>$request->get('location')['latitude']));

        $publication->save();
        $location->publication()->associate($publication);

        return response()->json($publication);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $publication = Publication::with('user','category','type','status')->findOrFail($id);

        if($publication->deleted == 1){
            return response()->json([ 'error' => 'Publication don\'t exit'], 401);
        }

        return response()->json($publication); //Get the resource
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

        $publication = Publication::with('user','site')->findOrFail($id); //Get the resource

        if(Gate::denies('destroy',$publication)) {
            return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
        }

        $publication->deleted = 1;
        if ($publication->site !== null){
            $publication->site->deleted = 1;
        }
        $publication->save();

        return response()->json($publication);
    }
}
