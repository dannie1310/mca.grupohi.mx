<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use Carbon\Carbon;
use App\Models\Ruta;
use App\Models\ProyectoLocal;
use App\Models\Origen;
use App\Models\Tiro;
use App\Models\TipoRuta;
use App\Models\Cronometria;

class RutasController extends Controller
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
        return view('rutas.index')
                ->withRutas(Ruta::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rutas.create')
                ->withOrigenes(Origen::all()->lists('Descripcion', 'IdOrigen'))
                ->withTiros(Tiro::all()->lists('Descripcion', 'IdTiro'))
                ->withTipos(TipoRuta::all()->lists('Descripcion', 'IdTipoRuta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateRutaRequest $request)
    {
        $proyecto_local = ProyectoLocal::where('IdProyectoGlobal', '=', $request->session()->get('id'))->first();
        $request->request->add(['IdProyecto' => $proyecto_local->IdProyecto]);
        $request->request->add(['FechaAlta' => Carbon::now()->toDateString()]);
        $request->request->add(['HoraAlta' => Carbon::now()->toTimeString()]);
        $request->request->add(['Registra' => auth()->user()->idusuario]);

        $ruta = Ruta::create($request->all());
        
        $cronometria = new Cronometria();
        $cronometria->IdRuta = $ruta->IdRuta;
        $cronometria->TiempoMinimo = $request->get('TiempoMinimo');
        $cronometria->Tolerancia = $request->get('Tolerancia');
        $cronometria->FechaAlta = Carbon::now()->toDateString();
        $cronometria->HoraAlta = Carbon::now()->toTimeString();
        $cronometria->Registra = auth()->user()->idusuario;
        $cronometria->save();
        
        Flash::success('¡RUTA REGISTRADA CORRECTAMENTE!');
        return redirect()->route('rutas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('rutas.show')
                ->withRuta(Ruta::findOrFail($id));
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
