<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\PublicationStatus;

class PublicationStatusesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(PublicationStatus::where('deleted', 0)->get());
    }
}
