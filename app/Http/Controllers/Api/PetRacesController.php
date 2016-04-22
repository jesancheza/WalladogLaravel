<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\PetRace;

class PetRacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id_type)
    {
        return response()->json(PetRace::where('deleted', 0)->where('id_pet_type', $id_type)->get());
    }
}
