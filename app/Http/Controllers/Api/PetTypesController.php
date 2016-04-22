<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\PetType;

class PetTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(PetType::where('deleted', 0)->get());
    }
}
