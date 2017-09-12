<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Empresa;
use App\Models\Sindicato;
use App\Models\Transformers\ConciliacionTransformer;
use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;
use App\Models\Conciliacion\Conciliacion;
use Illuminate\Support\Facades\DB;

class ConciliacionesController extends Controller
{
    
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:consultar-conciliacion', ['only' => ['index','edit']]);
       
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
        return view('conciliaciones.index')
                ->withContador(1)
                ->withConciliaciones($conciliaciones);
    }
    
    public function buscar($busqueda, $howMany = 15, $except = [])
    {//Venta::orderBy('idventa', 'DESC')->get(); $conciliaciones = Conciliacion::orderBy('idconciliacion', 'desc')            ->get();
        return Conciliacion::whereNotIn('idconciliacion', $except)
            ->leftJoin('empresas','empresas.IdEmpresa','=','conciliacion.idempresa')
            ->leftJoin('sindicatos','sindicatos.IdSindicato','=','conciliacion.idsindicato')
            ->where(function ($query) use($busqueda) {
                $query->where('sindicatos.Descripcion', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('sindicatos.NombreCorto', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('empresas.razonSocial', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('empresas.RFC', 'LIKE', '%'.$busqueda.'%')
                    ->orWhere('conciliacion.idconciliacion', 'LIKE', '%'.$busqueda.'%')    
                    ;
            })
            ->select(DB::raw("conciliacion.*"))
            ->groupBy('conciliacion.idconciliacion')
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
        return view('conciliaciones.create')
            ->withEmpresas(Empresa::orderBy('razonSocial', 'ASC')->lists('razonSocial', 'IdEmpresa'))
            ->withSindicatos(Sindicato::orderBy('nombreCorto', 'ASC')->lists('nombreCorto', 'IdSindicato'));
    }

    /**
     * @param Requests\CreateConciliacionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Requests\CreateConciliacionRequest $request)
    {

        $conciliacion = Conciliacion::create([
            'fecha_conciliacion' => $request->get('fecha'),
            'idsindicato'        => $request->get('idsindicato'),
            'idempresa'          => $request->get('idempresa'),
            'ImportePagado'      => $request->get('importe_pagado'),
            'VolumenPagado'      => $request->get('volumen_pagado'),
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
     * @param $id
     * @return mixed
     */
    public function edit(Request $request, $id)
    {
/*
        $duplicidad ="SELECT count(idviaje_neto) AS num,
                   conciliacion_detalle.idconciliacion_detalle,
                   conciliacion_detalle.idviaje_neto,
                   viajesnetos.Code,
                   group_concat(conciliacion.idconciliacion)
              FROM ((prod_sca_pista_aeropuerto_2.conciliacion_detalle conciliacion_detalle
                      INNER JOIN prod_sca_pista_aeropuerto_2.viajesnetos viajesnetos
                         ON     (conciliacion_detalle.idviaje_neto =
                                    viajesnetos.IdViajeNeto)
                            AND (viajesnetos.IdViajeNeto =
                                    conciliacion_detalle.idviaje_neto))
                     INNER JOIN prod_sca_pista_aeropuerto_2.conciliacion conciliacion
                        ON     (conciliacion_detalle.idconciliacion =
                                   conciliacion.idconciliacion)
                           AND (conciliacion.idconciliacion =
                                conciliacion_detalle.idconciliacion))
                   INNER JOIN prod_sca_pista_aeropuerto_2.viajes viajes
                      ON (viajes.IdViajeNeto = viajesnetos.IdViajeNeto)
             WHERE   conciliacion_detalle.estado = 1
                   AND conciliacion.idconciliacion = '{$id}'
            GROUP BY conciliacion_detalle.idviaje_neto, viajesnetos.Code
            HAVING count(idviaje_neto) > 1";

        $datos = DB::connection('sca')->select(DB::raw($duplicidad));
        $num ="";
        $code ="";
        foreach ($datos as $d) {
            $num = $d->num;
            $code =$d->Code;
        }*/
            return view('conciliaciones.edit')
                ->withConciliacion(Conciliacion::findOrFail($id))
                ->withCamiones(Camion::lists('Economico', 'IdCamion'));
               /* ->withDuplicados($num)
                ->withCode($code );*/

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
            $conciliacion = Conciliacion::findOrFail($id);

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
        $conciliacion = Conciliacion::findOrFail($id);
        $conciliacion->cancelar($request);

        return response()->json([
            'success' => true,
            'status_code' => 200
        ], 200);
    }

    public function show(Request $request, $id) {
        if($request->ajax()) {
            $conciliacion = ConciliacionTransformer::transform(Conciliacion::find($id));
            return response()->json([
                'status_code' => 200,
                'conciliacion' => $conciliacion
            ]);
        }

        $conciliacion = Conciliacion::find($id);

        return view('conciliaciones.show')
            ->withConciliacion($conciliacion);
    }
}
