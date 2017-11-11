<?php

namespace App\Http\Controllers;

use App\Models\ConciliacionSuministro\ConciliacionSuministro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ConciliacionesSuministroController extends Controller
{

    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        //$this->middleware('permission:consultar-conciliacion', ['only' => ['index','edit']]);

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $conciliaciones = $this->buscar($request->get('buscar'), 15);
        return view('control_suministro.conciliaciones.index')
            ->withContador(1)
            ->withConciliaciones($conciliaciones);
    }

    public function buscar($busqueda, $howMany = 15, $except = [])
    {//Venta::orderBy('idventa', 'DESC')->get(); $conciliaciones = Conciliacion::orderBy('idconciliacion', 'desc')            ->get();

       return ConciliacionSuministro::whereNotIn('idconciliacion', $except)
            ->leftJoin('empresas','empresas.IdEmpresa','=','conciliacion_suministro.idempresa')
            ->leftJoin('sindicatos','sindicatos.IdSindicato','=','conciliacion_suministro.idsindicato')
            ->where(function ($query) use($busqueda) {
                $query->where('sindicatos.Descripcion', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('sindicatos.NombreCorto', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('empresas.razonSocial', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('empresas.RFC', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('conciliacion_suministro.idconciliacion', 'LIKE', '%'.$busqueda.'%')
                ;
            })
            ->select(DB::raw("conciliacion_suministro.*"))
            ->groupBy('conciliacion_suministro.idconciliacion')
            ->orderBy('idconciliacion',"DESC")

            ->paginate($howMany);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
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
