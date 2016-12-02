<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Camion;
use App\Models\Sindicato;
use App\Models\Operador;
use App\Models\Marca;
use App\Models\Boton;

class CamionesController extends Controller
{
    
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
       
        parent::__construct();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('camiones.index')
                ->withCamiones(Camion::paginate(50));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('camiones.create')
                ->withSindicatos(Sindicato::all()->lists('Descripcion', 'IdSindicato'))
                ->withOperadores(Operador::all()->lists('Nombre', 'IdOperador'))
                ->withMarcas(Marca::all()->lists('Descripcion', 'IdMarca'))
                ->withBotones(Boton::all()->lists('Identificador', 'IdBoton'));
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
        return view('camiones.show')
                ->withCamion(Camion::findOrFail($id));
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
        //
    }
}
