<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\Image;

class ImagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Image::with('pet','user')->where('deleted', 0)->paginate(15));
    }
}
