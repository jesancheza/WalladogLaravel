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
use Walladog\Site;
use Walladog\SiteComment;

class SiteCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(SiteComment::with('site')->where('deleted', 0)->paginate(15));
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

        $validator = Validator::make($request->only(['site_id','title','comment']), [
            'site_id' => 'exists:sites,id|required',
            'title' =>  'string|max:100|required',
            'comment' => 'string|max:255|required'
        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $id_site = $request->get('site_id');
        $site = Site::with('user')->findOrFail($id_site);

        if($site->deleted == 1){
            return response()->json([ 'error' => 'Site don\'t exit'], 401);
        }

        $comment = new SiteComment();

        $comment->site_id = $request->get('site_id');
        $comment->title = $request->get('title');
        $comment->comment = $request->get('comment');
        $comment->user_id = Auth::id();
        $comment->deleted = 0;

        $comment->save();

        return response()->json($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = SiteComment::with('site')->findOrFail($id);

        if($comment->deleted == 1){
            return response()->json([ 'error' => 'Comment don\'t exit'], 401);
        }

        return response()->json($comment); //Get the resource
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

        $validator = Validator::make($request->only(['title','comment']), [
            'title' =>  'string|max:100|required',
            'comment' => 'string|max:255|required'
        ]);
        if ($validator->fails()) {
            return Response::make([
                'message'   => 'Validation Failed',
                'errors'        => $validator->errors()
            ]);
        }

        $comment = SiteComment::with('site')->findOrFail($id);

        if($comment->deleted == 1){
            return response()->json([ 'error' => 'Site comment don\'t exit'], 401);
        }

        if(Gate::denies('update',$comment)) {
            return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
        }
        
        $comment->title = $request->get('title');
        $comment->comment = $request->get('comment');

        $comment->update();

        return response()->json($comment);
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

        $comment = SiteComment::with('site')->findOrFail($id); //Get the resource

        if(Gate::denies('destroy',$comment)) {
            return response()->json([ 'error' => 'Usuario no autorizado' ], 401);
        }

        $comment->deleted = 1;
        $comment->save();

        return response()->json($comment);
    }
}
