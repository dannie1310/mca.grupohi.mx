<?php

namespace App\Http\Controllers;

use App\Models\CierrePeriodo;
use App\Models\MotivoCargaManual;
use App\Models\Tarifas\TarifaMaterial;
use App\Models\Tarifas\TarifaRutaMaterial;
use App\Models\Transformers\ViajeNetoTransformer;
use App\Models\ValidacionCierrePeriodo;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Camion;
use App\Models\Empresa;
use App\Models\Material;
use App\Models\Origen;
use App\Models\Sindicato;
use App\Models\Tiro;
use App\Models\ViajeNeto;
use App\Models\Viajes\Viajes;
use App\Models\FolioValeManual;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Zizaco\Entrust\Entrust;
use Auth;

class ViajesNetosController extends Controller
{

    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:ViajesNetosController', ['only' => ['permisos_store']]);


        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Viajes
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $tarifas = "";
            if ($request->get('action') == 'modificar') {

                $data = [];
                if($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                    ]);

                    $viajes = ViajeNeto::porValidar()
                        ->whereBetween('viajesnetos.FechaLlegada', [$request->get('FechaInicial'), $request->get('FechaFinal')])
                        ->get();

                    foreach ($viajes as $viaje) {

                        $data [] = [
                            'IdViajeNeto' => $viaje->IdViajeNeto,
                            'FechaLlegada' => $viaje->FechaLlegada,
                            'Tiro' => (String)$viaje->tiro,
                            'IdTiro' => $viaje->tiro ? $viaje->tiro->IdTiro : '',
                            'Camion' => (String)$viaje->camion,
                            'IdCamion' => $viaje->IdCamion,
                            'HoraLlegada' => $viaje->HoraLlegada,
                            'CubicacionCamion' => $viaje->CubicacionCamion,
                            'Origen' => (String)$viaje->origen,
                            'IdOrigen' => $viaje->origen ? $viaje->origen->IdOrigen : '',
                            'Material' => (String)$viaje->material,
                            'IdMaterial' => $viaje->material ? $viaje->material->IdMaterial : '',
                            'ShowModal' => false,
                            'IdSindicato' => $viaje->IdSindicato,
                            'IdEmpresa' => $viaje->IdEmpresa,
                            'Sindicato' => (String)$viaje->sindicato,
                            'Empresa' => (String)$viaje->empresa,
                            'Codigo' => $viaje->Code,
                            'cierres' => ViajeNeto::validandoCierre($viaje->FechaLlegada),
                            'denegado' => $viaje->denegado
                        ];
                    }
                } elseif ($request->tipo_busqueda == 'codigo') {
                    $this->validate($request, [
                        'Codigo' => 'required'
                    ]);

                    $viajes = ViajeNeto::porValidar()
                        ->where('viajesnetos.Code', '=', $request->Codigo)
                        ->get();

                    foreach ($viajes as $viaje) {
                        $data [] = [
                            'IdViajeNeto' => $viaje->IdViajeNeto,
                            'FechaLlegada' => $viaje->FechaLlegada,
                            'Tiro' => (String)$viaje->tiro,
                            'IdTiro' => $viaje->tiro ? $viaje->tiro->IdTiro : '',
                            'Camion' => (String)$viaje->camion,
                            'IdCamion' => $viaje->IdCamion,
                            'HoraLlegada' => $viaje->HoraLlegada,
                            'CubicacionCamion' => $viaje->CubicacionCamion,
                            'Origen' => (String)$viaje->origen,
                            'IdOrigen' => $viaje->origen ? $viaje->origen->IdOrigen : '',
                            'Material' => (String)$viaje->material,
                            'IdMaterial' => $viaje->material ? $viaje->material->IdMaterial : '',
                            'ShowModal' => false,
                            'IdSindicato' => $viaje->IdSindicato,
                            'IdEmpresa' => $viaje->IdEmpresa,
                            'Sindicato' => (String)$viaje->sindicato,
                            'Empresa' => (String)$viaje->empresa,
                            'Codigo' => $viaje->Code,
                            'cierres' => ViajeNeto::validandoCierre($viaje->FechaLlegada),
                            'denegado' => $viaje->denegado
                        ];
                    }
                }
            }

            else if($request->get('action') == 'detalle_conflicto'){
                $id_conflicto = $request->get("id_conflicto");
                $id_viaje = $request->get("id_viaje");
                //dd($id_viaje);
                $conflicto = \App\Models\Conflictos\ConflictoEntreViajes::find($id_conflicto);
                $viaje = ViajeNeto::find($id_viaje);
                $data["cierres"] = ViajeNeto::validandoCierre($viaje->FechaLlegada);
                $data["denegado"] = $viaje->denegado;
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
                            "id"=>$detalle->viaje_neto->IdViajeNeto,
                            "code"=>$detalle->viaje_neto->Code,
                            "fecha_registro"=>$detalle->viaje_neto->timestamp_carga->format("d-m-Y G:i:s"),
                            "fecha_salida"=>$detalle->viaje_neto->timestamp_salida->format("d-m-Y G:i:s"),
                            "fecha_llegada"=>$detalle->viaje_neto->timestamp_llegada->format("d-m-Y G:i:s"),
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
                    $query = ViajeNeto::EnConflicto()->Fechas($fechas);
                }else{
                    $query = ViajeNeto::EnConflicto()->Codigo($codigo);
                }


                $viajes_netos = $query->get();
                $datos =[];
                $data = ViajeNetoTransformer::transform($viajes_netos);
                foreach ($data as $dat) {
                    $datos [] = [
                        'id' => $dat['id'],
                        'autorizo' => $dat['autorizo'],
                        'camion' => $dat['camion'],
                        'Code' => $dat['codigo'],
                        'cubicacion' => $dat['cubicacion'],
                        'estado' => $dat['estado'],
                        'estatus' => $dat['estatus'],
                        'id_material' => $dat['id_material'],
                        'id_origen' => $dat['id_origen'],
                        'material' => $dat['material'],
                        'origen' => $dat['origen'],
                        'registro' => $dat['registro'],
                        'registro_primer_toque' => $dat['registro_primer_toque'],
                        'timestamp_llegada' => $dat['timestamp_llegada'],
                        'FechaLlegada' => $dat['fechaLlegada'],
                        'HoraLlegada' => $dat['horaLlegada'],
                        'tipo' => $dat['tipo'],
                        'tiro' => $dat['tiro'],
                        'importe' => $dat['importe'],
                        'valido' => $dat['valido'],
                        'conflicto' => $dat['conflicto'],
                        'conflicto_pdf' => $dat['conflicto_pdf'],
                        'conflicto_pagable' => $dat['conflicto_pagable'],
                        'cierre' => ViajeNeto::validandoCierre($dat['fechaLlegada']),
                        'fecha_hora_carga' => $dat['fecha_hora_carga']
                    ];
                }
                $data = $datos;
            }
            else if ($request->get('action') == 'validar') {
                $data = [];

                if($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                    ]);

                    $viajes = ViajeNeto::porValidar()
                        ->whereBetween('viajesnetos.FechaLlegada', [$request->get('FechaInicial'), $request->get('FechaFinal')])
                        ->get();
                    foreach ($viajes as $viaje) {
                        $tipo_origen = Origen::find($viaje->IdOrigen);

                        $tarifas_ruta_material = DB::connection('sca')->select(DB::raw("SELECT 
                                                    tarifas_ruta_material.*,
                                                    rutas.PrimerKM AS primer,
                                                    rutas.KMSubsecuentes AS subsecuentes,
                                                    rutas.KMAdicionales AS adicionales
                                                FROM
                                                    tarifas_ruta_material
                                                        INNER JOIN
                                                    rutas ON rutas.IdRuta = id_ruta
                                                WHERE
                                                    rutas.IdOrigen = ".$viaje->IdOrigen."
                                                        AND rutas.IdTiro = ".$viaje->IdTiro."
                                                        AND id_material = ".$viaje->IdMaterial."
                                                        AND tarifas_ruta_material.Estatus != 2
                                                        AND tarifas_ruta_material.inicio_vigencia <= '".$viaje->FechaLlegada."'
                                                        AND IFNULL(tarifas_ruta_material.fin_vigencia, NOW()) >= '".$viaje->FechaLlegada."'"));

                        foreach ($tarifas_ruta_material as $item) {
                            $tarifas [] = [
                                'id' => $item->id,
                                'primer_km' => $item->primer_km,
                                'km_subsecuente' => $item->km_subsecuentes,
                                'km_adicionales' => $item->km_adicionales,
                                'ruta_primer' => $item->primer,
                                'ruta_subsecuente' => $item->subsecuentes,
                                'ruta_adicional' => $item->adicionales
                            ];
                        }

                        $data [] = [
                            'Accion' => $viaje->valido($tarifas) ? 1 : 0,
                            'IdViajeNeto' => $viaje->IdViajeNeto,
                            'FechaLlegada' => $viaje->FechaLlegada,
                            'Tiro' => (String)$viaje->tiro,
                            'Camion' => (String)$viaje->camion,
                            'HoraLlegada' => $viaje->HoraLlegada,
                            'Cubicacion' => $viaje->CubicacionCamion,
                            'Origen' => (String )$viaje->origen,
                            'IdOrigen' => $viaje->IdOrigen,
                            'IdSindicato' => isset($viaje->IdSindicato) ? $viaje->IdSindicato : '',
                            'IdEmpresa' => isset($viaje->IdEmpresa) ? $viaje->IdEmpresa : '',
                            'Material' => (String)$viaje->material,
                            'Tiempo' => Carbon::createFromTime(0, 0, 0)->addSeconds($viaje->getTiempo())->toTimeString(),
                            'Ruta' => isset($viaje->ruta) ? $viaje->ruta->present()->claveRuta : "",
                            'primer' => isset($viaje->ruta) ? $viaje->ruta->present()->PrimerKm : "",
                            'subsecuente' => isset($viaje->ruta) ? $viaje->ruta->present()->KmSubsecuentes : "",
                            'adicional' =>isset($viaje->ruta) ? $viaje->ruta->present()->KmAdicionales : "",
                            'Code' => isset($viaje->Code) ? $viaje->Code : "",
                            'Valido' => $viaje->valido(),
                            'ShowModal' => false,
                            'Distancia' => $viaje->ruta ? $viaje->ruta->TotalKM : null,
                            'Estado' => $viaje->estado(),
                            'Importe' => $viaje->ruta ? $viaje->getImporte() : null,
                            'PrimerKM' => ($viaje->material->tarifaMaterial) ? $viaje->material->tarifaMaterial->PrimerKM : 0,
                            'KMSubsecuente' => ($viaje->material->tarifaMaterial) ? $viaje->material->tarifaMaterial->KMSubsecuente : 0,
                            'KMAdicional' => ($viaje->material->tarifaMaterial) ? $viaje->material->tarifaMaterial->KMAdicional : 0,
                            'Tara' => 0,
                            'Bruto' => 0,
                            'TipoTarifa' => 'm',
                            'TipoFDA' => 'm',
                            'cierre' => ViajeNeto::validandoCierre($viaje->FechaLlegada),
                            'Imagenes' => $viaje->imagenes,
                            'denegado' => (String) $viaje->denegado,
                            'tipo_origen' => $tipo_origen->interno,
                            'tarifas_ruta_material' => $tarifas_ruta_material
                        ];
                    }

                } else if($request->tipo_busqueda == 'codigo') {
                    $this->validate($request, [
                        'Codigo' => 'required'
                    ]);
                    $viajes = ViajeNeto::porValidar()
                        ->where('viajesnetos.Code', '=', $request->Codigo)
                        ->get();
                    foreach($viajes as $viaje) {
                        $tipo_origen = Origen::find($viaje->IdOrigen);

                        $tarifas_ruta_material = DB::connection('sca')->select(DB::raw("SELECT 
                                                    tarifas_ruta_material.*,
                                                    rutas.PrimerKM AS primer,
                                                    rutas.KMSubsecuentes AS subsecuentes,
                                                    rutas.KMAdicionales AS adicionales
                                                FROM
                                                    tarifas_ruta_material
                                                        INNER JOIN
                                                    rutas ON rutas.IdRuta = id_ruta
                                                WHERE
                                                    rutas.IdOrigen = ".$viaje->IdOrigen."
                                                        AND rutas.IdTiro = ".$viaje->IdTiro."
                                                        AND id_material = ".$viaje->IdMaterial."
                                                        AND tarifas_ruta_material.Estatus != 2
                                                        AND tarifas_ruta_material.inicio_vigencia <= '".$viaje->FechaLlegada."'
                                                        AND IFNULL(tarifas_ruta_material.fin_vigencia, NOW()) >= '".$viaje->FechaLlegada."'"));

                        foreach ($tarifas_ruta_material as $item) {
                            $tarifas [] = [
                                'id' => $item->id,
                                'primer_km' => $item->primer_km,
                                'km_subsecuente' => $item->km_subsecuentes,
                                'km_adicionales' => $item->km_adicionales,
                                'ruta_primer' => $item->primer,
                                'ruta_subsecuente' => $item->subsecuentes,
                                'ruta_adicional' => $item->adicionales
                            ];
                        }

                        $data [] = [
                            'Accion' => $viaje->valido() ? 1 : 0,
                            'IdViajeNeto' => $viaje->IdViajeNeto,
                            'FechaLlegada' => $viaje->FechaLlegada,
                            'Tiro' => (String)$viaje->tiro,
                            'Camion' => (String)$viaje->camion,
                            'HoraLlegada' => $viaje->HoraLlegada,
                            'Cubicacion' => $viaje->CubicacionCamion,
                            'Origen' => (String )$viaje->origen,
                            'IdOrigen' => $viaje->IdOrigen,
                            'IdSindicato' => isset($viaje->IdSindicato) ? $viaje->IdSindicato : '',
                            'IdEmpresa' => isset($viaje->IdEmpresa) ? $viaje->IdEmpresa : '',
                            'Material' => (String)$viaje->material,
                            'Tiempo' => Carbon::createFromTime(0, 0, 0)->addSeconds($viaje->getTiempo())->toTimeString(),
                            'Ruta' => isset($viaje->ruta) ? $viaje->ruta->present()->claveRuta : "",
                            'primer' => isset($viaje->ruta) ? $viaje->ruta->present()->PrimerKm : "",
                            'subsecuente' => isset($viaje->ruta) ? $viaje->ruta->present()->KmSubsecuentes : "",
                            'adicional' =>isset($viaje->ruta) ? $viaje->ruta->present()->KmAdicionales : "",
                            'Code' => isset($viaje->Code) ? $viaje->Code : "",
                            'Valido' => $viaje->valido(),
                            'ShowModal' => false,
                            'Distancia' => $viaje->ruta ? $viaje->ruta->TotalKM : null,
                            'Estado' => $viaje->estado(),
                            'Importe' => $viaje->ruta ? $viaje->getImporte() : null,
                            'PrimerKM' => ($viaje->material->tarifaMaterial) ? $viaje->material->tarifaMaterial->PrimerKM : 0,
                            'KMSubsecuente' => ($viaje->material->tarifaMaterial) ? $viaje->material->tarifaMaterial->KMSubsecuente : 0,
                            'KMAdicional' => ($viaje->material->tarifaMaterial) ? $viaje->material->tarifaMaterial->KMAdicional : 0,
                            'Tara' => 0,
                            'Bruto' => 0,
                            'TipoTarifa' => 'm',
                            'TipoFDA' => 'm',
                            'cierre' => ViajeNeto::validandoCierre($viaje->FechaLlegada),
                            'Imagenes' => $viaje->imagenes,
                            'denegado' => (String) $viaje->denegado,
                            'tipo_origen' => $tipo_origen->interno,
                            'tarifas_ruta_material' => $tarifas
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
                    $query = DB::connection('sca')->table('viajesnetos')->select('viajesnetos.*')->whereNull('viajesnetos.IdViajeNeto');
                    $query = ViajeNeto::scopeReporte($query);

                    foreach($request->get('Tipo', []) as $tipo) {
                        if($tipo == 'CM_C') {
                            $q_cmc = DB::connection('sca')->table('viajesnetos');
                            $q_cmc = ViajeNeto::scopeRegistradosManualmente($q_cmc);
                            $q_cmc = ViajeNeto::scopeReporte($q_cmc);
                            $q_cmc = ViajeNeto::scopeFechas($q_cmc, $fechas);
                            $q_cmc = ViajeNeto::scopeConciliados($q_cmc, $request->Estado);
                            $query->union($q_cmc);
                        }
                        if($tipo == 'CM_A') {
                            $q_cma = DB::connection('sca')->table('viajesnetos');
                            $q_cma = ViajeNeto::scopeManualesAutorizados($q_cma);
                            $q_cma = ViajeNeto::scopeReporte($q_cma);
                            $q_cma = ViajeNeto::scopeFechas($q_cma, $fechas);
                            $q_cma = ViajeNeto::scopeConciliados($q_cma, $request->Estado);
                            $query->union($q_cma);
                        }
                        if($tipo == 'CM_V') {
                            $q_cmv = DB::connection('sca')->table('viajesnetos');
                            $q_cmv = ViajeNeto::scopeManualesValidados($q_cmv);
                            $q_cmv = ViajeNeto::scopeReporte($q_cmv);
                            $q_cmv = ViajeNeto::scopeFechas($q_cmv, $fechas);
                            $q_cmv = ViajeNeto::scopeConciliados($q_cmv, $request->Estado);
                            $query->union($q_cmv);
                        }
                        if($tipo == 'CM_R') {
                            $q_cmr = DB::connection('sca')->table('viajesnetos');
                            $q_cmr = ViajeNeto::scopeManualesRechazados($q_cmr);
                            $q_cmr = ViajeNeto::scopeReporte($q_cmr);
                            $q_cmr = ViajeNeto::scopeFechas($q_cmr, $fechas);
                            $q_cmr = ViajeNeto::scopeConciliados($q_cmr, $request->Estado);
                            $query->union($q_cmr);
                        }
                        if($tipo == 'CM_D') {
                            $q_cmd = DB::connection('sca')->table('viajesnetos');
                            $q_cmd = ViajeNeto::scopeManualesDenegados($q_cmd);
                            $q_cmd = ViajeNeto::scopeReporte($q_cmd);
                            $q_cmd = ViajeNeto::scopeFechas($q_cmd, $fechas);
                            $q_cmd = ViajeNeto::scopeConciliados($q_cmd, $request->Estado);
                            $query->union($q_cmd);
                        }
                        if($tipo == 'M_V') {
                            $q_mv = DB::connection('sca')->table('viajesnetos');
                            $q_mv = ViajeNeto::scopeMovilesValidados($q_mv);
                            $q_mv = ViajeNeto::scopeReporte($q_mv);
                            $q_mv = ViajeNeto::scopeFechas($q_mv, $fechas);
                            $q_mv = ViajeNeto::scopeConciliados($q_mv, $request->Estado);
                            $query->union($q_mv);
                        }
                        if($tipo == 'M_A') {
                            $q_ma = DB::connection('sca')->table('viajesnetos');
                            $q_ma = ViajeNeto::scopeMovilesAutorizados($q_ma);
                            $q_ma = ViajeNeto::scopeReporte($q_ma);
                            $q_ma = ViajeNeto::scopeFechas($q_ma, $fechas);
                            $q_ma = ViajeNeto::scopeConciliados($q_ma, $request->Estado);
                            $query->union($q_ma);
                        }
                        if($tipo == 'M_D') {
                            $q_md = DB::connection('sca')->table('viajesnetos');
                            $q_md = ViajeNeto::scopeMovilesDenegados($q_md);
                            $q_md = ViajeNeto::scopeReporte($q_md);
                            $q_md = ViajeNeto::scopeFechas($q_md, $fechas);
                            $q_md = ViajeNeto::scopeConciliados($q_md, $request->Estado);
                            $query->union($q_md);
                        }
                    }
                } else if($request->tipo_busqueda == 'codigo') {
                    $this->validate($request, [
                        'Codigo' => 'required'
                    ]);
                    $query = DB::connection('sca')->table('viajesnetos')->select('viajesnetos.*')->where('viajesnetos.Code', '=', $request->Codigo);
                    $query = ViajeNeto::scopeReporte($query);
                }

                $viajes_netos = $query->get();
                $data = ($viajes_netos);
                return response()->json(['viajes_netos' => $data]);
            } else if($request->get('action') == 'corte') {
                $this->validate($request, [
                    'turnos' => 'required|array',
                    'fecha'  => 'required|date_format:"Y-m-d"'
                ]);

                $viajes_netos = ViajeNeto::scopeCorte();

                $turno_1 = $turno_2 = false;
                foreach($request->get('turnos', []) as $turno) {
                    if($turno == '1') {
                        $turno_1 = true;
                        $timestamp_inicial_1 = $request->get('fecha') . ' 07:00:00';
                        $timestamp_final_1 = $request->get('fecha') . ' 18:59:59';
                    }
                    if($turno == '2') {
                        $turno_2 = true;
                        $fecha = Carbon::createFromFormat('Y-m-d', $request->get('fecha'))->addDay(1)->toDateString();
                        $timestamp_inicial_2 = $request->fecha . ' 19:00:00';
                        $timestamp_final_2 = $fecha . ' 06:59:59';
                    }
                }

                if($turno_1 && $turno_2) {
                    $viajes_netos->where(function ($query) use ($timestamp_final_1, $timestamp_final_2, $timestamp_inicial_1, $timestamp_inicial_2){
                        $query->whereRaw("CAST(CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) AS datetime) between '{$timestamp_inicial_1}' and '{$timestamp_final_1}'")
                            ->orWhereRaw("CAST(CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) AS datetime) between '{$timestamp_inicial_2}' and '{$timestamp_final_2}'");
                    });
                } else if($turno_1 && ! $turno_2) {
                    $viajes_netos->whereRaw("CAST(CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) AS datetime) between '{$timestamp_inicial_1}' and '{$timestamp_final_1}'");
                } else if(! $turno_1 && $turno_2) {
                    $viajes_netos->whereRaw("CAST(CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) AS datetime) between '{$timestamp_inicial_2}' and '{$timestamp_final_2}'");
                }
                $data = $viajes_netos->get();
            }
            return response()->json(['viajes_netos' => $data]);

        } else {
            if($request->type =='excel') {
                if($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                        'Tipo' => 'required|array',
                        'Estado' => 'required'
                    ]);

                    $fechas = $request->only(['FechaInicial', 'FechaFinal']);
                    $query = DB::connection('sca')->table('viajesnetos')->select('viajesnetos.*')->whereNull('viajesnetos.IdViajeNeto');
                    $query = ViajeNeto::scopeReporte($query);

                    $tipos = [];

                    foreach($request->get('Tipo', []) as $tipo) {
                        if($tipo == 'CM_C') {
                            array_push($tipos, 'Manuales - Cargados');
                            $q_cmc = DB::connection('sca')->table('viajesnetos');
                            $q_cmc = ViajeNeto::scopeRegistradosManualmente($q_cmc);
                            $q_cmc = ViajeNeto::scopeReporte($q_cmc);
                            $q_cmc = ViajeNeto::scopeFechas($q_cmc, $fechas);
                            $q_cmc = ViajeNeto::scopeConciliados($q_cmc, $request->Estado);
                            $query->union($q_cmc);
                        }
                        if($tipo == 'CM_A') {
                            array_push($tipos, 'Manuales - Autorizados (Pend. Validar)');
                            $q_cma = DB::connection('sca')->table('viajesnetos');
                            $q_cma = ViajeNeto::scopeManualesAutorizados($q_cma);
                            $q_cma = ViajeNeto::scopeReporte($q_cma);
                            $q_cma = ViajeNeto::scopeFechas($q_cma, $fechas);
                            $q_cma = ViajeNeto::scopeConciliados($q_cma, $request->Estado);
                            $query->union($q_cma);
                        }
                        if($tipo == 'CM_V') {
                            array_push($tipos, 'Manuales - Validados');
                            $q_cmv = DB::connection('sca')->table('viajesnetos');
                            $q_cmv = ViajeNeto::scopeManualesValidados($q_cmv);
                            $q_cmv = ViajeNeto::scopeReporte($q_cmv);
                            $q_cmv = ViajeNeto::scopeFechas($q_cmv, $fechas);
                            $q_cmv = ViajeNeto::scopeConciliados($q_cmv, $request->Estado);
                            $query->union($q_cmv);
                        }
                        if($tipo == 'CM_R') {
                            array_push($tipos, 'Manuales - Rechazados');
                            $q_cmr = DB::connection('sca')->table('viajesnetos');
                            $q_cmr = ViajeNeto::scopeManualesRechazados($q_cmr);
                            $q_cmr = ViajeNeto::scopeReporte($q_cmr);
                            $q_cmr = ViajeNeto::scopeFechas($q_cmr, $fechas);
                            $q_cmr = ViajeNeto::scopeConciliados($q_cmr, $request->Estado);
                            $query->union($q_cmr);
                        }
                        if($tipo == 'CM_D') {
                            array_push($tipos, 'Manuales - Denegados');
                            $q_cmd = DB::connection('sca')->table('viajesnetos');
                            $q_cmd = ViajeNeto::scopeManualesDenegados($q_cmd);
                            $q_cmd = ViajeNeto::scopeReporte($q_cmd);
                            $q_cmd = ViajeNeto::scopeFechas($q_cmd, $fechas);
                            $q_cmd = ViajeNeto::scopeConciliados($q_cmd, $request->Estado);
                            $query->union($q_cmd);
                        }
                        if($tipo == 'M_V') {
                            array_push($tipos, 'Móviles - Validados');
                            $q_mv = DB::connection('sca')->table('viajesnetos');
                            $q_mv = ViajeNeto::scopeMovilesValidados($q_mv);
                            $q_mv = ViajeNeto::scopeReporte($q_mv);
                            $q_mv = ViajeNeto::scopeFechas($q_mv, $fechas);
                            $q_mv = ViajeNeto::scopeConciliados($q_mv, $request->Estado);
                            $query->union($q_mv);
                        }
                        if($tipo == 'M_A') {
                            array_push($tipos, 'Móviles - Pendientes de Validar');
                            $q_ma = DB::connection('sca')->table('viajesnetos');
                            $q_ma = ViajeNeto::scopeMovilesAutorizados($q_ma);
                            $q_ma = ViajeNeto::scopeReporte($q_ma);
                            $q_ma = ViajeNeto::scopeFechas($q_ma, $fechas);
                            $q_ma = ViajeNeto::scopeConciliados($q_ma, $request->Estado);
                            $query->union($q_ma);
                        }
                        if($tipo == 'M_D') {
                            array_push($tipos, 'Móviles - Denegados');
                            $q_md = DB::connection('sca')->table('viajesnetos');
                            $q_md = ViajeNeto::scopeMovilesDenegados($q_md);
                            $q_md = ViajeNeto::scopeReporte($q_md);
                            $q_md = ViajeNeto::scopeFechas($q_md, $fechas);
                            $q_md = ViajeNeto::scopeConciliados($q_md, $request->Estado);
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

                    $query = DB::connection('sca')->table('viajesnetos')->select('viajesnetos.*')->where('viajesnetos.Code', '=', $request->Codigo);
                    $query = ViajeNeto::scopeReporte($query);

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
                if(auth()->user()->can('consultar-viajes-conflicto')){
                    return view('viajes_netos.index')
                        ->withAction('en_conflicto');}
                        else{
                            Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                            return redirect()->back();
                        }
            } else {
                if(auth()->user()->can('consulta-viajes')){
                    return view('viajes_netos.index')
                        ->withAction('');
                }else{
                    Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                    return redirect()->back();
                }
            }
        }
    }

    /**
     * Show the form for creating new resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(auth()->user()->can('ingresar-viajes-manuales')) {
           return view('viajes_netos.create')
                ->withMotivos(MotivoCargaManual::orderBy('id','ASC')->lists('descripcion','id'))
                ->withCamiones(Camion::whereRaw('Estatus = 1')->orderBy('Economico', 'ASC')->lists('Economico', 'IdCamion'))
                ->withOrigenes(Origen::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdOrigen'))
                ->withTiros(Tiro::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdTiro'))
                ->withMateriales(Material::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdMaterial'))
                ->withFolios(FolioValeManual::whereRaw('id_viaje_neto is null')->lists('folio','id'))
                ->withAction($request->get('action'));


        }else{
            Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
            return redirect()->back();
        }
    }

    /**
     * Store newly created resources in storage.
     *
     * @param Requests\CreateViajeNetoRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateViajeNetoRequest $request)
    {
        return response()->json(ViajeNeto::cargaManual($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $viaje_neto = ViajeNeto::findOrFail($id);
        return response()->json(ViajeNetoTransformer::transform($viaje_neto));
    }

    /**
     * Show the form for editing all the resources.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        if($request->get('action') == 'validar') {
            if(auth()->user()->can('validar-viajes')) {

                return view('viajes_netos.edit')
                    ->withSindicatos(Sindicato::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdSindicato'))
                    ->withEmpresas(Empresa::orderBy('razonSocial', 'ASC')->lists('razonSocial', 'IdEmpresa'))
                    ->withCierre(CierrePeriodo::cierresPeriodos())
                    ->withAction('validar');
            }
                else{
                    Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                    return redirect()->back();
                }
        } else if($request->get('action') == 'autorizar') {
            if(auth()->user()->can('autorizar-viajes-manuales')) {

                return view('viajes_netos.edit')
                    ->withViajes(ViajeNeto::registradosManualmente()->get())
                    ->withCierre(CierrePeriodo::cierresPeriodos())
                    ->withAction('autorizar');
            }else{
                Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                return redirect()->back();
            }
        } else if($request->get('action') == 'modificar') {
            if(auth()->user()->can('modificar-viajes')) {
                return view('viajes_netos.edit')
                    ->withOrigenes(Origen::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdOrigen'))
                    ->withTiros(Tiro::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdTiro'))
                    ->withCamiones(Camion::orderBy('Economico', 'ASC')->lists('Economico', 'IdCamion'))
                    ->withMateriales(Material::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdMaterial'))
                    ->withEmpresas(Empresa::orderBy('razonSocial', 'ASC')->lists('razonSocial', 'IdEmpresa'))
                    ->withSindicatos(Sindicato::orderBy('Descripcion', 'ASC')->lists('Descripcion', 'IdSindicato'))
                    ->withAction('modificar');
            } else {
                Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                return redirect()->back();
            }
        }
    }

    /**
     * Update the resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if($request->get('type') == 'validar') {
            //dd($request['data']);
            $cubicacionNva=$request['data']['Cubicacion'];
            $viaje_neto = ViajeNeto::findOrFail($request->get('IdViajeNeto'));

            if($viaje_neto->CubicacionCamion != 0 && $cubicacionNva>$viaje_neto->CubicacionCamion){
                throw new \Exception('La cubicación del camión no debe superar '.$viaje_neto->CubicacionCamion.' m3');
            }

            if($request['data']['Accion']==0){
                FolioValeManual::where('folio', '=', $viaje_neto->Code)->update(['id_viaje_neto' => NULL]);
            }
            if($request['data']['Accion']==1) {
                if ($request['data']['IdSindicato'] == "") {
                    throw new \Exception('Debe seleccionar un sindicato para validar dicho viaje');
                }

                if ($request['data']['IdEmpresa'] == "") {
                    throw new \Exception('Debe seleccionar una empresa para validar dicho viaje');
                }

                if ($request['data']['TipoTarifa'] == "" && ($request['data']['importe'] == "" || $request['data']['importe'] == 0)) {
                    throw new \Exception('Debe seleccionar una tarifa para validar dicho viaje');
                }
                if ($request['data']['TipoTarifa'] == "" || ($request['data']['importe'] == "" || $request['data']['importe'] == 0)) {
                    if ($request['data']['TipoTarifa'] == "m") {
                        throw new \Exception('No existe una tarifa por material registrada para el viaje.');
                    } else {
                        throw new \Exception('No existe una tarifa por ruta más material registrada para el viaje.');
                    }
                }
            }
            return response()->json($viaje_neto->validar($request));
        } else if($request->path() == 'viajes_netos/autorizar') {

            $msg = ViajeNeto::autorizar($request->get('Estatus'));
            Flash::success($msg);
            return redirect()->back();
        } else if($request->get('type') == 'modificar') {

            $cubicacionNva=$request['data']['CubicacionCamion'];
            $viaje_neto = ViajeNeto::findOrFail($request->get('IdViajeNeto'));
            if($viaje_neto->CubicacionCamion != 0 && $cubicacionNva>$viaje_neto->CubicacionCamion){
                throw new \Exception('La cubicación del camión no debe superar '.$viaje_neto->CubicacionCamion.' m/3');
            }
            return response()->json($viaje_neto->modificar($request));
        }else if($request->get('type') == 'poner_pagable') {


            $viaje_neto = ViajeNeto::findOrFail($request->get('IdViajeNeto'));
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
