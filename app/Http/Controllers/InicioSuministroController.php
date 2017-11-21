<?php

namespace App\Http\Controllers;

use App\Models\InicioCamion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class InicioSuministroController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)//editar!!!
    {
        if($request->ajax()) {
            if ($request->get('action') == 'modificar') {//modificar cuando se use

                $data = [];
                if($request->tipo_busqueda == 'fecha') {
                    $this->validate($request, [
                        'FechaInicial' => 'required|date_format:"Y-m-d"',
                        'FechaFinal' => 'required|date_format:"Y-m-d"',
                    ]);

                    $viajes = InicioCamion::porValidar()
                        ->whereBetween('inicio_camion.FechaSalida', [$request->get('FechaInicial'), $request->get('FechaFinal')])
                        ->get();

                    foreach ($viajes as $viaje) {
                    dd($viaje);
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
                            'cierres' => ViajeNeto::validandoCierre($viaje->FechaLlegada)
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
                            'cierres' => ViajeNeto::validandoCierre($viaje->FechaLlegada)
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
                            'Imagenes' => $viaje->imagenes
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
                            'Imagenes' => $viaje->imagenes
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
                    $query = DB::connection('sca')->table('inicio_camion')->select('inicio_camion.*')->whereNull('inicio_camion.id');
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
                            $q_cma = DB::connection('sca')->table('viajesnetos');
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
            if ($request->type == 'excel') {
                if ($request->tipo_busqueda == 'fecha') {
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

                    foreach ($request->get('Tipo', []) as $tipo) {
                        if ($tipo == 'CM_C') {
                            array_push($tipos, 'Manuales - Cargados');
                            $q_cmc = DB::connection('sca')->table('viajesnetos');
                            $q_cmc = ViajeNeto::scopeRegistradosManualmente($q_cmc);
                            $q_cmc = ViajeNeto::scopeReporte($q_cmc);
                            $q_cmc = ViajeNeto::scopeFechas($q_cmc, $fechas);
                            $q_cmc = ViajeNeto::scopeConciliados($q_cmc, $request->Estado);
                            $query->union($q_cmc);
                        }
                        if ($tipo == 'CM_A') {
                            array_push($tipos, 'Manuales - Autorizados (Pend. Validar)');
                            $q_cma = DB::connection('sca')->table('viajesnetos');
                            $q_cma = ViajeNeto::scopeManualesAutorizados($q_cma);
                            $q_cma = ViajeNeto::scopeReporte($q_cma);
                            $q_cma = ViajeNeto::scopeFechas($q_cma, $fechas);
                            $q_cma = ViajeNeto::scopeConciliados($q_cma, $request->Estado);
                            $query->union($q_cma);
                        }
                        if ($tipo == 'CM_V') {
                            array_push($tipos, 'Manuales - Validados');
                            $q_cmv = DB::connection('sca')->table('viajesnetos');
                            $q_cmv = ViajeNeto::scopeManualesValidados($q_cmv);
                            $q_cmv = ViajeNeto::scopeReporte($q_cmv);
                            $q_cmv = ViajeNeto::scopeFechas($q_cmv, $fechas);
                            $q_cmv = ViajeNeto::scopeConciliados($q_cmv, $request->Estado);
                            $query->union($q_cmv);
                        }
                        if ($tipo == 'CM_R') {
                            array_push($tipos, 'Manuales - Rechazados');
                            $q_cmr = DB::connection('sca')->table('viajesnetos');
                            $q_cmr = ViajeNeto::scopeManualesRechazados($q_cmr);
                            $q_cmr = ViajeNeto::scopeReporte($q_cmr);
                            $q_cmr = ViajeNeto::scopeFechas($q_cmr, $fechas);
                            $q_cmr = ViajeNeto::scopeConciliados($q_cmr, $request->Estado);
                            $query->union($q_cmr);
                        }
                        if ($tipo == 'CM_D') {
                            array_push($tipos, 'Manuales - Denegados');
                            $q_cmd = DB::connection('sca')->table('viajesnetos');
                            $q_cmd = ViajeNeto::scopeManualesDenegados($q_cmd);
                            $q_cmd = ViajeNeto::scopeReporte($q_cmd);
                            $q_cmd = ViajeNeto::scopeFechas($q_cmd, $fechas);
                            $q_cmd = ViajeNeto::scopeConciliados($q_cmd, $request->Estado);
                            $query->union($q_cmd);
                        }
                        if ($tipo == 'M_V') {
                            array_push($tipos, 'Móviles - Validados');
                            $q_mv = DB::connection('sca')->table('viajesnetos');
                            $q_mv = ViajeNeto::scopeMovilesValidados($q_mv);
                            $q_mv = ViajeNeto::scopeReporte($q_mv);
                            $q_mv = ViajeNeto::scopeFechas($q_mv, $fechas);
                            $q_mv = ViajeNeto::scopeConciliados($q_mv, $request->Estado);
                            $query->union($q_mv);
                        }
                        if ($tipo == 'M_A') {
                            array_push($tipos, 'Móviles - Pendientes de Validar');
                            $q_ma = DB::connection('sca')->table('viajesnetos');
                            $q_ma = ViajeNeto::scopeMovilesAutorizados($q_ma);
                            $q_ma = ViajeNeto::scopeReporte($q_ma);
                            $q_ma = ViajeNeto::scopeFechas($q_ma, $fechas);
                            $q_ma = ViajeNeto::scopeConciliados($q_ma, $request->Estado);
                            $query->union($q_ma);
                        }
                        if ($tipo == 'M_D') {
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
                    if (auth()->user()->can('consultar-viajes-conflicto')) {
                        return view('viajes_netos.index')
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
