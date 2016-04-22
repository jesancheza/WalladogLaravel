<?php

namespace Walladog\Http\Controllers\Api;

use Illuminate\Http\Request;

use Walladog\Http\Controllers\Controller;
use Walladog\Http\Requests;
use Walladog\SiteCategory;

class SiteCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(SiteCategory::where('deleted', 0)->get());
    }
}
