<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class XLSTableroController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('context');
        //$this->middleware('permission:descargar-excel-conciliacion', ['only' => ['conciliacion']]);

        parent::__construct();
    }


    public function novalidados(){
        dd("aqui");
    }

}
