<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Origen;

class OrigenesTirosController extends Controller
{
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
       
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $id_origen
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id_origen)
    {
        if($request->ajax()) {
            return response()->json(Origen::findOrFail($id_origen)->tiros->lists('Descripcion', 'IdTiro'));
        }
    }
}
