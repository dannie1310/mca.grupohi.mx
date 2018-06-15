<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\ConflictosSuministros\InicioSuministroPagable;
use App\Models\InicioCamion;
use App\Models\Material;
use App\Models\Origen;
use App\Models\Transformers\InicioCamionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\CierrePeriodo;
use Carbon\Carbon;
use phpDocumentor\Reflection\Types\String_;


class InicioSuministroController extends Controller
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
    public function index(Request $request)//editar!!!
    {
        if($request->ajax()) {
            if ($request->get('action') == 'modificar') {

                $data = [];
                if($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                    ]);

                    $viajes = InicioCamion::porValidar()
                        ->whereBetween('inicio_camion.fecha_origen', [$request->get('FechaInicial'), $request->get('FechaFinal')])
                        ->get();

                    foreach ($viajes as $viaje) {
                        $data [] = [
                            'IdViajeNeto' => $viaje->id,
                            'FechaLlegada' => $viaje->fecha_origen,
                            'Camion' => (String)$viaje->camion,
                            'Cubicacion' => $viaje->cubicacion,
                            'Origen' => (String )$viaje->origen,
                            'IdOrigen' => $viaje->idorigen,
                            'Material' => (String)$viaje->material,
                            'IdMaterial' => (String)$viaje->idmaterial,
                            'Code' => (String) $viaje->code,
                            'Valido' => $viaje->valido(),
                            'ShowModal' => false,
                            'Estado' => $viaje->estado(),
                            'FolioMina' => $viaje->folioMina,
                            'FolioSeguimiento' => $viaje->folioSeguimiento,
                            'Volumen'=>$viaje->volumen,
                            'cierre' => InicioCamion::validandoCierre($viaje->fecha_origen)
                        ];
                    }
                } elseif ($request->tipo_busqueda == 'codigo') {
                    $this->validate($request, [
                        'Codigo' => 'required'
                    ]);

                    $viajes = InicioCamion::porValidar()
                        ->where('inicio_camion.code', '=', $request->Codigo)
                        ->get();

                    foreach ($viajes as $viaje) {
                        $data [] = [
                            'IdViajeNeto' => $viaje->id,
                            'FechaLlegada' => $viaje->fecha_origen,
                            'Camion' => (String)$viaje->camion,
                            'Cubicacion' => $viaje->cubicacion,
                            'Origen' => (String )$viaje->origen,
                            'IdOrigen' => $viaje->idorigen,
                            'Material' => (String)$viaje->material,
                            'IdMaterial' => (String)$viaje->idmaterial,
                            'Code' => $viaje->code,
                            'Valido' => $viaje->valido(),
                            'ShowModal' => false,
                            'Estado' => $viaje->estado(),
                            'FolioMina' => $viaje->folioMina,
                            'FolioSeguimiento' => $viaje->folioSeguimiento,
                            'Volumen'=>$viaje->volumen,
                            'cierre' => InicioCamion::validandoCierre($viaje->fecha_origen),
                        ];
                    }
                }
            }

            else if($request->get('action') == 'detalle_conflicto'){
                $id_conflicto = $request->get("id_conflicto");
                $id_viaje = $request->get("id_viaje");
                $conflicto = \App\Models\ConflictosSuministros\ConflictoSuministro::find($id_conflicto);
                $viaje = InicioCamion::find($id_viaje);
                $data["cierres"] = InicioCamion::validandoCierre($viaje->fecha_origen);
                $pagable = $viaje->conflicto_pagable;
                $detalles = $conflicto->detalles;
                if($pagable){
                    $data["motivo"] = $pagable->motivo;
                    $data["aprobo_pago"] = (String) $pagable->usuario_aprobo_pago;
                }
                else{
                    $data["motivo"] = null;
                    $data["aprobo_pago"] = null;
                }
                foreach($detalles as $detalle){
                    $data["conflictos"][] = [
                        "id"=>$detalle->viaje_neto->id,
                        "code"=>$detalle->viaje_neto->code,
                        "fecha_registro"=>$detalle->viaje_neto->fecha_origen,
                        "folioMina"=>$detalle->viaje_neto->folioMina,
                        "folioSeg"=>$detalle->viaje_neto->folioSeguimiento,
                        "volumen"=>$detalle->viaje_neto->volumen
                    ];
                }
                //dd(response()->json( $data));
                return response()->json( $data);
            }
            else if($request->get('action') == 'en_conflicto'){

                if($request->get("tipo_busqueda") == "fecha"){
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                    ]);
                    $fechas = $request->only(['FechaInicial', 'FechaFinal']);
                }
                else{

                    $codigo = $request->get("Codigo");
                    if($codigo == ""){
                        $codigo = null;
                    }else{
                        $codigo = $request->get("Codigo");
                    }

                }

                if($request->get("tipo_busqueda") == "fecha"){
                    $query = InicioCamion::EnConflicto()->Fechas($fechas);
                }else{
                    $query = InicioCamion::EnConflicto()->Codigo($codigo);
                }


                $viajes_netos = $query->get();
                $datos =[];
             //   dd($viajes_netos);
                $data = InicioCamionTransformer::transform($viajes_netos);
             //   dd($data);
                foreach ($data as $dat) {

                    $datos [] = [
                        'id' => $dat['id'],
                        'autorizo' => $dat['autorizo'],
                        'camion' => $dat['camion'],
                        'code' => $dat['codigo'],
                        'cubicacion' => $dat['cubicacion'],
                        'estado' => $dat['estado'],
                        'estatus' => $dat['estatus'],
                        'idmaterial' => $dat['idmaterial'],
                        'idorigen' => $dat['idorigen'],
                        'material' => $dat['material'],
                        'origen' => $dat['origen'],
                        'registro' => $dat['registro'],
                        'fecha_origen' => $dat['timestamp_llegada'],
                        'tipo' => $dat['tipo'],
                        'valido' => $dat['valido'],
                        'motivo' => $dat['motivo'],
                        'conflicto' => $dat['conflicto'],
                        'conflicto_pdf' => $dat['conflicto_pdf'],
                        'conflicto_pagable' => $dat['conflicto_pagable'],
                        'cierre' => InicioCamion::validandoCierre($dat['timestamp_llegada']),
                        'fecha_hora_carga' => $dat['fecha_hora_carga']
                    ];
                }
                $data = $datos;

            }
            else if ($request->get('action') == 'validar') {
                $data = [];
               // dd("aqui7");
                if($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                    ]);

                    $viajes = InicioCamion::porValidar()
                        ->whereBetween('inicio_camion.fecha_origen', [$request->get('FechaInicial'), $request->get('FechaFinal')])
                        ->get();

                    foreach ($viajes as $viaje) {

                        $data [] = [
                            'Accion' => $viaje->valido() ? 1 : 0,
                            'IdViajeNeto' => $viaje->id,
                            'FechaLlegada' => $viaje->fecha_origen,
                            'Camion' => (String)$viaje->camion,
                            'Cubicacion' => $viaje->cubicacion,
                            'Origen' => (String )$viaje->origen,
                            'IdOrigen' => $viaje->IdOrigen,
                            'Material' => (String)$viaje->material,
                            'Code' => isset($viaje->code) ? $viaje->code : "",
                            'Valido' => $viaje->valido(),
                            'ShowModal' => false,
                            'Estado' => $viaje->estado(),
                            'FolioMina' => $viaje->folioMina,
                            'FolioSeguimiento' => $viaje->folioSeguimiento,
                            'Volumen'=>$viaje->deductiva,
                            'tipo' =>$viaje->tipo,
                            'cierre' => InicioCamion::validandoCierre($viaje->fecha_origen),
                        ];
                    }
                  //  dd($data);

                } else if($request->tipo_busqueda == 'codigo') {
                    $this->validate($request, [
                        'Codigo' => 'required'
                    ]);
                    $viajes = InicioCamion::porValidar()
                        ->where('inicio_camion.code', '=', $request->Codigo)
                        ->get();
                    foreach($viajes as $viaje) {
                        $data [] = [
                            'Accion' => $viaje->valido() ? 1 : 0,
                            'IdViajeNeto' => $viaje->id,
                            'FechaLlegada' => $viaje->fecha_origen,
                            'Camion' => (String)$viaje->camion,
                            'Cubicacion' => $viaje->cubicacion,
                            'Origen' => (String )$viaje->origen,
                            'IdOrigen' => $viaje->IdOrigen,
                            'Material' => (String)$viaje->material,
                            'Code' => isset($viaje->code) ? $viaje->code : "",
                            'Valido' => $viaje->valido(),
                            'ShowModal' => false,
                            'Estado' => $viaje->estado(),
                            'FolioMina' => $viaje->folioMina,
                            'FolioSeguimiento' => $viaje->folioSeguimiento,
                            'Volumen'=>$viaje->volumen,
                            'cierre' => InicioCamion::validandoCierre($viaje->fecha_origen),
                        ];
                    }
                }
            } else if ($request->get('action') == 'index') {

                if($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                        'Tipo' => 'required|array',
                        'Estado' => 'required'
                    ]);

                    $fechas = $request->only(['FechaInicial', 'FechaFinal']);
                    $query = DB::connection('sca')->table('inicio_camion')->addSelect("inicio_camion.*")->whereNull('inicio_camion.id');
                    $query = InicioCamion::scopeReporte($query);

                    foreach($request->get('Tipo', []) as $tipo) {
                        if($tipo == 'CM_C') {
                            $q_cmc = DB::connection('sca')->table('inicio_camion');
                            $q_cmc = InicioCamion::scopeRegistradosManualmente($q_cmc);
                            $q_cmc = InicioCamion::scopeReporte($q_cmc);
                            $q_cmc = InicioCamion::scopeFechas($q_cmc, $fechas);
                            $q_cmc = InicioCamion::scopeConciliados($q_cmc, $request->Estado);
                            $query->union($q_cmc);
                        }
                        if($tipo == 'CM_A') {
                            $q_cma = DB::connection('sca')->table('inicio_camion');
                            $q_cma = InicioCamion::scopeManualesAutorizados($q_cma);
                            $q_cma = InicioCamion::scopeReporte($q_cma);
                            $q_cma = InicioCamion::scopeFechas($q_cma, $fechas);
                            $q_cma = InicioCamion::scopeConciliados($q_cma, $request->Estado);
                            $query->union($q_cma);
                        }
                        if($tipo == 'CM_V') {
                            $q_cmv = DB::connection('sca')->table('inicio_camion');
                            $q_cmv = InicioCamion::scopeManualesValidados($q_cmv);
                            $q_cmv = InicioCamion::scopeReporte($q_cmv);
                            $q_cmv = InicioCamion::scopeFechas($q_cmv, $fechas);
                            $q_cmv = InicioCamion::scopeConciliados($q_cmv, $request->Estado);
                            $query->union($q_cmv);
                        }
                        if($tipo == 'CM_R') {
                            $q_cmr = DB::connection('sca')->table('inicio_camion');
                            $q_cmr = InicioCamion::scopeManualesRechazados($q_cmr);
                            $q_cmr = InicioCamion::scopeReporte($q_cmr);
                            $q_cmr = InicioCamion::scopeFechas($q_cmr, $fechas);
                            $q_cmr = InicioCamion::scopeConciliados($q_cmr, $request->Estado);
                            $query->union($q_cmr);
                        }
                        if($tipo == 'CM_D') {
                            $q_cmd = DB::connection('sca')->table('inicio_camion');
                            $q_cmd = InicioCamion::scopeManualesDenegados($q_cmd);
                            $q_cmd = InicioCamion::scopeReporte($q_cmd);
                            $q_cmd = InicioCamion::scopeFechas($q_cmd, $fechas);
                            $q_cmd = InicioCamion::scopeConciliados($q_cmd, $request->Estado);
                            $query->union($q_cmd);
                        }
                        if($tipo == 'M_V') {
                           // dd("aqui");
                            $q_mv = DB::connection('sca')->table('inicio_camion');
                            $q_mv = InicioCamion::scopeMovilesValidados($q_mv);
                            $q_mv = InicioCamion::scopeReporte($q_mv);
                            $q_mv = InicioCamion::scopeFechas($q_mv, $fechas);
                            $q_mv = InicioCamion::scopeConciliados($q_mv, $request->Estado);
                            $query->union($q_mv);
                        }
                        if($tipo == 'M_A') {
                            $q_ma = DB::connection('sca')->table('inicio_camion');
                            $q_ma = InicioCamion::scopeMovilesAutorizados($q_ma);
                            $q_ma = InicioCamion::scopeReporte($q_ma);
                            $q_ma = InicioCamion::scopeFechas($q_ma, $fechas);
                            $q_ma = InicioCamion::scopeConciliados($q_ma, $request->Estado);
                            $query->union($q_ma);
                        }
                        if($tipo == 'M_D') {
                            $q_md = DB::connection('sca')->table('inicio_camion');
                            $q_md = InicioCamion::scopeMovilesDenegados($q_md);
                            $q_md = InicioCamion::scopeReporte($q_md);
                            $q_md = InicioCamion::scopeFechas($q_md, $fechas);
                            $q_md = InicioCamion::scopeConciliados($q_md, $request->Estado);
                            $query->union($q_md);
                        }
                    }
                } else if($request->tipo_busqueda == 'codigo') {
                    $this->validate($request, [
                        'Codigo' => 'required'
                    ]);
                    $query = DB::connection('sca')->table('inicio_camion')->select('inicio_camion.*')->where('inicio_camion.code', '=', $request->Codigo);
                    $query = InicioCamion::scopeReporte($query);
                }

                $viajes_netos = $query->get();
                $data = ($viajes_netos);
                return response()->json(['viajes_netos' => $data]);
            }
            return response()->json(['viajes_netos' => $data]);

        } else {
            if ($request->type == 'excel') {
                if ($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                        'Tipo' => 'required|array',
                        'Estado' => 'required'
                    ]);

                    $fechas = $request->only(['FechaInicial', 'FechaFinal']);
                    $query = DB::connection('sca')->table('inicio_camion')->select('inicio_camion.*')->whereNull('inicio_camion.id');
                    $query = InicioCamion::scopeReporte($query);

                    $tipos = [];

                    foreach ($request->get('Tipo', []) as $tipo) {
                        if ($tipo == 'CM_C') {
                            array_push($tipos, 'Manuales - Cargados');
                            $q_cmc = DB::connection('sca')->table('viajesnetos');
                            $q_cmc = InicioCamion::scopeRegistradosManualmente($q_cmc);
                            $q_cmc = InicioCamion::scopeReporte($q_cmc);
                            $q_cmc = InicioCamion::scopeFechas($q_cmc, $fechas);
                            $q_cmc = InicioCamion::scopeConciliados($q_cmc, $request->Estado);
                            $query->union($q_cmc);
                        }
                        if ($tipo == 'CM_A') {
                            array_push($tipos, 'Manuales - Autorizados (Pend. Validar)');
                            $q_cma = DB::connection('sca')->table('viajesnetos');
                            $q_cma = InicioCamion::scopeManualesAutorizados($q_cma);
                            $q_cma = InicioCamion::scopeReporte($q_cma);
                            $q_cma = InicioCamion::scopeFechas($q_cma, $fechas);
                            $q_cma = InicioCamion::scopeConciliados($q_cma, $request->Estado);
                            $query->union($q_cma);
                        }
                        if ($tipo == 'CM_V') {
                            array_push($tipos, 'Manuales - Validados');
                            $q_cmv = DB::connection('sca')->table('viajesnetos');
                            $q_cmv = InicioCamion::scopeManualesValidados($q_cmv);
                            $q_cmv = InicioCamion::scopeReporte($q_cmv);
                            $q_cmv = InicioCamion::scopeFechas($q_cmv, $fechas);
                            $q_cmv = InicioCamion::scopeConciliados($q_cmv, $request->Estado);
                            $query->union($q_cmv);
                        }
                        if ($tipo == 'CM_R') {
                            array_push($tipos, 'Manuales - Rechazados');
                            $q_cmr = DB::connection('sca')->table('viajesnetos');
                            $q_cmr = InicioCamion::scopeManualesRechazados($q_cmr);
                            $q_cmr = InicioCamion::scopeReporte($q_cmr);
                            $q_cmr = InicioCamion::scopeFechas($q_cmr, $fechas);
                            $q_cmr = InicioCamion::scopeConciliados($q_cmr, $request->Estado);
                            $query->union($q_cmr);
                        }
                        if ($tipo == 'CM_D') {
                            array_push($tipos, 'Manuales - Denegados');
                            $q_cmd = DB::connection('sca')->table('viajesnetos');
                            $q_cmd = InicioCamion::scopeManualesDenegados($q_cmd);
                            $q_cmd = InicioCamion::scopeReporte($q_cmd);
                            $q_cmd = InicioCamion::scopeFechas($q_cmd, $fechas);
                            $q_cmd = InicioCamion::scopeConciliados($q_cmd, $request->Estado);
                            $query->union($q_cmd);
                        }
                        if ($tipo == 'M_V') {
                            array_push($tipos, 'Móviles - Validados');
                            $q_mv = DB::connection('sca')->table('viajesnetos');
                            $q_mv = InicioCamion::scopeMovilesValidados($q_mv);
                            $q_mv = InicioCamion::scopeReporte($q_mv);
                            $q_mv = InicioCamion::scopeFechas($q_mv, $fechas);
                            $q_mv = InicioCamion::scopeConciliados($q_mv, $request->Estado);
                            $query->union($q_mv);
                        }
                        if ($tipo == 'M_A') {
                            array_push($tipos, 'Móviles - Pendientes de Validar');
                            $q_ma = DB::connection('sca')->table('viajesnetos');
                            $q_ma = InicioCamion::scopeMovilesAutorizados($q_ma);
                            $q_ma = InicioCamion::scopeReporte($q_ma);
                            $q_ma = InicioCamion::scopeFechas($q_ma, $fechas);
                            $q_ma = InicioCamion::scopeConciliados($q_ma, $request->Estado);
                            $query->union($q_ma);
                        }
                        if ($tipo == 'M_D') {
                            array_push($tipos, 'Móviles - Denegados');
                            $q_md = DB::connection('sca')->table('viajesnetos');
                            $q_md = InicioCamion::scopeMovilesDenegados($q_md);
                            $q_md = InicioCamion::scopeReporte($q_md);
                            $q_md = InicioCamion::scopeFechas($q_md, $fechas);
                            $q_md = InicioCamion::scopeConciliados($q_md, $request->Estado);
                            $query->union($q_md);
                        }
                    }

                    $viajes_netos = $query->get();
                    $data = [
                        'viajes_netos' => $viajes_netos,
                        'tipos' => $tipos,
                        'estado' => $request->Estado == 'C' ? 'Conciliados' : ($request->Estado == 'NC' ? 'No Conciliados' : 'Todos'),
                        'rango' => "DEL ({$request->get('FechaInicial')}) AL ({$request->get('FechaFinal')})"
                    ];
                } else if ($request->tipo_busqueda == 'codigo') {
                    $this->validate($request, [
                        'Codigo' => 'required'
                    ]);

                    $query = DB::connection('sca')->table('inicio_camion')->select('inicio_camion.*')->where('inicio_camion.code', '=', $request->Codigo);
                    $query = InicioCamion::scopeReporte($query);

                    $viajes_netos = $query->get();
                    $data = [
                        'viajes_netos' => $viajes_netos,
                        'tipos' => ['N/A'],
                        'estado' => 'N/A',
                        'rango' => "N/A"
                    ];
                }

                return (new Viajes($data))->excel();
            } else
                if ($request->get('action') == 'en_conflicto') {
                    if (auth()->user()->can('consultar-viajes-conflicto')) {
                        return view('control_suministro.suministro_netos.index')
                            ->withAction('en_conflicto');
                    } else {
                        Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                        return redirect()->back();
                    }
                }else {
                    if (auth()->user()->can('consulta-viajes')) {
                        return view('control_suministro.suministro_netos.index')
                            ->withAction('');
                    } else {
                        Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                        return redirect()->back();
                    }
                }
        }
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
    public function edit(Request $request)
    {
        //
        if($request->get('action') == 'validar') {
            if(auth()->user()->can('validar-viajes')) {
                return view('control_suministro.edit')
                   ->withCierre(CierrePeriodo::cierresPeriodos())
                    ->withAction('validar');
            }
            else{
                Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                return redirect()->back();
            }
        }
        else if($request->get('action') == 'modificar') {
            if(auth()->user()->can('modificar-viajes')) {
                return view('control_suministro.edit')
                    ->withOrigenes(Origen::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdOrigen'))
                    ->withCamiones(Camion::orderBy('Economico', 'ASC')->lists('Economico', 'IdCamion'))
                    ->withMateriales(Material::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdMaterial'))
                   ->withAction('modificar');
            } else {
                Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                return redirect()->back();
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if($request->get('type') == 'validar') {
            $cubicacionNva=$request['data']['Cubicacion'];
            $folioSeguimiento =$request['data']['FolioSeguimiento'];
            $folioMina =$request['data']['FolioMina'];
            $volumen =$request['data']['Volumen'];
            $viaje_neto = InicioCamion::findOrFail($request->get('IdViajeNeto'));
            if($request['data']['Accion']== 1 && ($folioMina==null || $folioSeguimiento ==null || $volumen == 0)){
                throw new \Exception('Ingregar el folio de mina, el de seguimiento y volumen');
            }
            if($viaje_neto->CubicacionCamion != 0 && $cubicacionNva>$viaje_neto->CubicacionCamion){
                throw new \Exception('La cubicación del camión no debe superar '.$viaje_neto->CubicacionCamion.' m3');
            }

            return response()->json($viaje_neto->validar($request));
        }else if($request->get('type') == 'modificar') {

            $cubicacionNva=$request['data']['Cubicacion'];
            $folioSeguimiento =$request['data']['FolioSeguimiento'];
            $folioMina =$request['data']['FolioMina'];
            $volumen =$request['data']['Volumen'];

            $viaje_neto = InicioCamion::findOrFail($request->get('IdViajeNeto'));

            /*if($folioMina==null || $folioSeguimiento ==null || $volumen == 0){
                throw new \Exception('Ingregar el folio de mina, el de seguimiento y volumen');
            }*/
            if($viaje_neto->CubicacionCamion != 0 && $cubicacionNva>$viaje_neto->CubicacionCamion){
                throw new \Exception('La cubicación del camión no debe superar '.$viaje_neto->CubicacionCamion.' m/3');
            }
            return response()->json($viaje_neto->modificar($request));

        }else if($request->get('type') == 'poner_pagable') {
            $viaje_neto = InicioCamion::findOrFail($request->get('IdViajeNeto'));
            return response()->json($viaje_neto->poner_pagable($request));
        }
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
