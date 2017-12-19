<?php

namespace App\Http\Controllers;


use App\Models\Camion;
use App\Models\Transformers\ConciliacionSuministroTransformer;
use App\Models\Transformers\ConciliacionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Sindicato;
use App\Models\ConciliacionSuministro\ConciliacionSuministro;
use Carbon\Carbon;

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
        return view('control_suministro.conciliaciones.create')
            ->withEmpresas(Empresa::orderBy('razonSocial', 'ASC')->lists('razonSocial', 'IdEmpresa'))
            ->withSindicatos(Sindicato::orderBy('nombreCorto', 'ASC')->lists('nombreCorto', 'IdSindicato'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateConciliacionRequest $request)
    {
        $conciliacion = ConciliacionSuministro::create([
            'fecha_conciliacion' => $request->get('fecha'),
            'idsindicato'        => $request->get('idsindicato'),
            'idempresa'          => $request->get('idempresa'),
            'fecha_inicial'      => Carbon::now()->toDateString(),
            'fecha_final'        => Carbon::now()->toDateString(),
            'estado'             => 0,
            'IdRegistro'         => auth()->user()->idusuario,
            'Folio'              => $request->get('folio'),
        ]);

        return response()->json([
            'success' => true,
            'status_code' => 200,
            'conciliacion' => $conciliacion
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        return view('control_suministro.conciliaciones.edit')
            ->withConciliacion(ConciliacionSuministro::findOrFail($id))
            ->withCamiones(Camion::lists('Economico', 'IdCamion'));
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
        if($request->ajax()) {
            $conciliacion = ConciliacionSuministro::findOrFail($id);

            if($request->get('action') == 'cerrar') {
                $conciliacion->cerrar($id);
            } else if ($request->get('action') == 'aprobar') {
                $conciliacion->aprobar();
            } else if($request->get('action') == 'detalles') {

                $this->validate($request, [
                    'importe_pagado' => 'required|numeric',
                    'volumen_pagado' => 'required|numeric'
                ]);

                $conciliacion->cambiar_detalles($request->get('importe_pagado'), $request->get('volumen_pagado'));

                return response()->json([
                    'status_code' => 200,
                    'importe_pagado_sf' => $conciliacion->ImportePagado,
                    'volumen_pagado_sf' => $conciliacion->VolumenPagado,
                    'volumen_pagado' => $conciliacion->volumen_pagado_f,
                    'importe_pagado' => $conciliacion->importe_pagado_f,
                ]);
            }

            return response()->json([
                'status_code' => 200,
                'success' => true
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $conciliacion = ConciliacionSuministro::findOrFail($id);
        $conciliacion->cancelar($request);

        return response()->json([
            'success' => true,
            'status_code' => 200
        ], 200);
    }

    public function show(Request $request, $id) {

        if($request->ajax()) {
            $conciliacion = ConciliacionSuministroTransformer::transform(ConciliacionSuministro::find($id));

            return response()->json([
                'status_code' => 200,
                'conciliacion' => $conciliacion
            ]);
        }

        $conciliacion = ConciliacionSuministro::find($id);

        return view('control_suministro.conciliaciones.show')
            ->withConciliacion($conciliacion);
    }
}
