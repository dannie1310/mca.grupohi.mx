<?php

namespace App\Models\ConciliacionSuministro;

use Carbon\Carbon;
use App\Models\InicioCamion;
use App\Models\InicioViaje;
use App\Models\Transformers\ConciliacionSuministroDetalleNoConciliadoTransformer;
use App\Models\Transformers\ConciliacionSuministroDetalleTransformer;
use Illuminate\Database\Eloquent\Model;
use App\Models\ConciliacionSuministro\ConciliacionSuministroDetalle;
use User;

class ConciliacionesSuministro
{
    /**
     * @var Conciliacion
     */
    protected $conciliacion;

    /**
     * @var
     */
    protected $data;

    protected $i = 0;

    /**
     * Conciliaciones constructor.
     * @param Conciliacion $conciliacion
     * @internal param $data
     */
    public function __construct(ConciliacionSuministro $conciliacion)
    {
        $this->conciliacion = $conciliacion;
    }

    public function procesaArregloIds($ids){
        $i = 0;
        foreach ($ids as $key => $id_viaje) {
            $v_ba = InicioViaje::find($id_viaje);
            $evaluacion = $this->evalua_viaje($v_ba->Code,$v_ba);
            if($evaluacion["detalle"] !== FALSE){
                $this->registraDetalle($evaluacion["detalle"]);
                $i++;
            }else{
                $this->registraDetalleNoConciliado($evaluacion["detalle_nc"]);
            }
        }
        $detalles = ConciliacionSuministroDetalleTransformer::transform(ConciliacionSuministroDetalle::where('idconciliacion', '=', $this->conciliacion->idconciliacion)->get());
        $detalles_nc = ConciliacionSuministroDetalleNoConciliadoTransformer::transform(ConciliacionSuministroDetalleNoConciliado::where('idconciliacion', '=', $this->conciliacion->idconciliacion)->get());

        return [
            'status_code' => 201,
            'registros'   => $i,
            'detalles'    => $detalles,
            'detalles_nc'    => $detalles_nc,
            'importe'     => $this->conciliacion->importe_f,
            'volumen'     => $this->conciliacion->volumen_f,
            'rango'       => $this->conciliacion->rango,
            'importe_viajes_manuales' => $this->conciliacion->importe_viajes_manuales_f,
            'volumen_viajes_manuales' => $this->conciliacion->volumen_viajes_manuales_f,
            'volumen_viajes_moviles' => $this->conciliacion->volumen_viajes_moviles_f,
            'importe_viajes_moviles' => $this->conciliacion->importe_viajes_moviles_f,
            'porcentaje_importe_viajes_manuales' => $this->conciliacion->porcentaje_importe_viajes_manuales,
            'porcentaje_volumen_viajes_manuales' => $this->conciliacion->porcentaje_volumen_viajes_manuales
        ];
    }

    public function procesaCodigo($code){
        $evaluacion = $this->evalua_viaje($code);
        if($evaluacion["detalle"] !== FALSE){
            $detalle_conciliado = $this->registraDetalle($evaluacion["detalle"]);
            $detalle_c = ConciliacionSuministroDetalleTransformer::transform($detalle_conciliado);
            return [
                'status_code' => 201,
                'registros'   => 1,
                'detalles'    => $detalle_c,
                'importe'     => $this->conciliacion->importe_f,
                'volumen'     => $this->conciliacion->volumen_f,
                'rango'       => $this->conciliacion->rango,
                'importe_viajes_manuales' => $this->conciliacion->importe_viajes_manuales_f,
                'volumen_viajes_manuales' => $this->conciliacion->volumen_viajes_manuales_f,
                'volumen_viajes_moviles' => $this->conciliacion->volumen_viajes_moviles_f,
                'importe_viajes_moviles' => $this->conciliacion->importe_viajes_moviles_f,
                'porcentaje_importe_viajes_manuales' => $this->conciliacion->porcentaje_importe_viajes_manuales,
                'porcentaje_volumen_viajes_manuales' => $this->conciliacion->porcentaje_volumen_viajes_manuales
            ];
        }else{
            $detalle_no_conciliado = $this->registraDetalleNoConciliado($evaluacion["detalle_nc"]);
            $detalles_nc = ConciliacionSuministroDetalleNoConciliadoTransformer::transform($detalle_no_conciliado);
            return [
                'status_code' => 500,
                'detalles_nc'    => $detalles_nc,
            ];
        }
        //return $this->registraDetalleConciliacion($code, $viaje_neto, $viaje_pendiente_conciliar, $viaje_validado);
    }

    public function cargarExcel(UploadedFile $data) {
        $reader = Excel::load($data->getRealPath())->get();
        $i = 0;
        $y = 0;
        foreach ($reader as $row) {
            if ($row->codigo != null) {
                $evaluacion = $this->evalua_viaje($row->codigo);
                if($evaluacion["detalle"] !== FALSE){
                    $this->registraDetalle($evaluacion["detalle"]);
                    $i++;
                }else{
                    $this->registraDetalleNoConciliado($evaluacion["detalle_nc"]);
                    $y++;
                }
            } else {
                $camion = Camion::where('economico', $row->camion)->first();
                $viaje_neto = InicioCamion::where('IdCamion', $camion ? $camion->IdCamion : null)->where('FechaSalida', $row->fecha_llegada)->where('HoraSalida', $row->hora_llegada)->first();
                $complemento = 'Camión: '.$row->camion .' Fecha Llegada: '. $row->fecha_llegada->format("d-m-Y").' Hora Llegada: '. $row->hora_llegada->format("h:i:s");
                $evaluacion = $this->evalua_viaje(null, null, $viaje_neto, $complemento);
                if($evaluacion["detalle"] !== FALSE){
                    $this->registraDetalle($evaluacion["detalle"]);
                    $i++;
                }else{
                    $this->registraDetalleNoConciliado($evaluacion["detalle_nc"]);
                    $y++;
                }
            }
        }
        $detalles = ConciliacionSuministroDetalleNoConciliadoTransformer::transform(ConciliacionSuministroDetalle::where('idconciliacion', '=', $this->conciliacion->idconciliacion)->get());
        $detalles_nc = ConciliacionSuministroDetalleNoConciliadoTransformer::transform(ConciliacionSuministroDetalleNoConciliado::where('idconciliacion', '=', $this->conciliacion->idconciliacion)->get());

        return [
            'status_code' => 201,
            'registros'   => $i,
            'registros_nc'   => $y,
            'detalles'    => $detalles,
            'detalles_nc'    => $detalles_nc,
            'importe'     => $this->conciliacion->importe_f,
            'volumen'     => $this->conciliacion->volumen_f,
            'rango'       => $this->conciliacion->rango,
            'importe_viajes_manuales' => $this->conciliacion->importe_viajes_manuales_f,
            'volumen_viajes_manuales' => $this->conciliacion->volumen_viajes_manuales_f,
            'volumen_viajes_moviles' => $this->conciliacion->volumen_viajes_moviles_f,
            'importe_viajes_moviles' => $this->conciliacion->importe_viajes_moviles_f,
            'porcentaje_importe_viajes_manuales' => $this->conciliacion->porcentaje_importe_viajes_manuales,
            'porcentaje_volumen_viajes_manuales' => $this->conciliacion->porcentaje_volumen_viajes_manuales
        ];

    }

    public function cargarExcelProcesoCompleto(UploadedFile $data) {
        $fecha_conciliacion = $this->conciliacion->fecha_conciliacion;
        $fecha_minima = Carbon::createFromFormat('Y-m-d', '2017-04-09');
        if(!($fecha_minima->format("Ymd")>=$fecha_conciliacion->format("Ymd"))){
            throw new \Exception("Esta concilación no puede ser procesada con la opción: Carga Excel Completa");
        }
        $reader = Excel::load($data->getRealPath())->get();
        $i = 0;
        $y = 0;
        //dd("aqui", $reader);
        foreach ($reader as $row) {
            if($row->camion){
                $codigo_evaluar = (str_replace(" ", "", $row->ticket) == "MANUAL" || $row->ticket == NULL)?NULL: $row->ticket;
                if(strlen($codigo_evaluar)<10 && $codigo_evaluar){
                    $codigo_evaluar = str_repeat("0",10- strlen($codigo_evaluar)).$codigo_evaluar;
                }
                $this->procesamientoCompletoViaje($codigo_evaluar, $row);
            }
        }

        $detalles = ConciliacionSuministroDetalleTransformer::transform(ConciliacionSuministroDetalle::where('idconciliacion', '=', $this->conciliacion->idconciliacion)->get());
        $detalles_nc = ConciliacionSuministroDetalleNoConciliadoTransformer::transform(ConciliacionSuministroDetalleNoConciliado::where('idconciliacion', '=', $this->conciliacion->idconciliacion)->get());

        return [
            'status_code' => 201,
            'registros'   => count($detalles),
            'registros_nc'   => (count($detalles_nc)),
            'detalles'    => $detalles,
            'detalles_nc'    => $detalles_nc,
            'importe'     => $this->conciliacion->importe_f,
            'volumen'     => $this->conciliacion->volumen_f,
            'rango'       => $this->conciliacion->rango,
            'importe_viajes_manuales' => $this->conciliacion->importe_viajes_manuales_f,
            'volumen_viajes_manuales' => $this->conciliacion->volumen_viajes_manuales_f,
            'volumen_viajes_moviles' => $this->conciliacion->volumen_viajes_moviles_f,
            'importe_viajes_moviles' => $this->conciliacion->importe_viajes_moviles_f,
            'porcentaje_importe_viajes_manuales' => $this->conciliacion->porcentaje_importe_viajes_manuales,
            'porcentaje_volumen_viajes_manuales' => $this->conciliacion->porcentaje_volumen_viajes_manuales
        ];

    }

    private function procesamientoCompletoViaje($codigo_evaluar, $datos_viaje){
        $datos_viaje->ticket = $codigo_evaluar;
        $viaje_neto = $this->getViajeNeto($codigo_evaluar,$datos_viaje);
        //DD($viaje_neto,$row->ticket);
    }

    private function getOrigen($descripcion){
        $origen = Origen::where('Descripcion', $descripcion)
            ->first();
        if(!$origen){
//            $origen = Origen::create([
//                "IdTipoOrigen"=>1,
//                "IdProyecto"=>1,
//                "Descripcion"=>$descripcion,
//                "FechaAlta"=>Carbon::now()->toDateString(),
//                "HoraAlta"=>Carbon::now()->toTimeString(),
//            ]);
        }
        return $origen;
    }

    private function getTiro($descripcion){
        $tiro = Tiro::where('Descripcion', $descripcion)
            ->first();
        if(!$tiro){
//            $tiro = Tiro::create([
//                "IdProyecto"=>1,
//                "Descripcion"=>$descripcion,
//                "FechaAlta"=>Carbon::now()->toDateString(),
//                "HoraAlta"=>Carbon::now()->toTimeString(),
//            ]);
        }
        return $tiro;
    }
    private function getRuta($origen, $tiro){
        if($origen){
            $id_origen = $origen->IdOrigen;
        }
        if($tiro){
            $id_tiro = $tiro->IdTiro;
        }
        if($origen && $tiro){
            $ruta = Ruta::where('IdOrigen', $id_origen)
                ->where('IdTiro', $id_tiro)
                ->first();

            if(!$ruta){
                $ruta = $this->registraRuta($id_origen, $id_tiro);
            }
        }else{
            $ruta = null;
        }
        return $ruta;
    }
    private function registraRuta($id_origen, $id_tiro){

        $datos_ruta["IdProyecto"] = 1;
        $datos_ruta["IdTipoRuta"] = 1;
        $datos_ruta["IdOrigen"] = $id_origen;
        $datos_ruta["IdTiro"] = $id_tiro;
        $datos_ruta["PrimerKm"] = 1;
        $datos_ruta["KmSubsecuentes"] = 49;
        $datos_ruta["KmAdicionales"] = 0;
        $datos_ruta["TotalKM"] = 50;
        $datos_ruta["FechaAlta"] = Carbon::now()->toDateString();
        $datos_ruta["HoraAlta"] = Carbon::now()->toTimeString();
        $datos_ruta["Registra"] = auth()->user()->idusuario;

        $ruta = Ruta::create($datos_ruta);
        $this->registraCronometria($ruta->IdRuta);
        return $ruta;

    }
    private function getCronometria($ruta){
        if($ruta){
            $cronometria = $ruta->cronometria;
            if(!$cronometria){
                $this->registraCronometria($ruta->IdRuta);
            }
        }else{
            $cronometria = null;
        }
        return $cronometria;
    }
    private function registraCronometria($id_ruta){
        $cronometria = new Cronometria();
        $cronometria->IdRuta = $id_ruta;
        $cronometria->TiempoMinimo = 1;
        $cronometria->Tolerancia = 1;
        $cronometria->FechaAlta = Carbon::now()->toDateString();
        $cronometria->HoraAlta = Carbon::now()->toTimeString();
        $cronometria->Registra = auth()->user()->idusuario;
        $cronometria->save();
    }
    private function getMaterial($descripcion){
        $material = Material::where('Descripcion', $descripcion)
            ->first();
//        if(!$material){
//            $material = Material::create([
//                "IdProyecto"=>1,
//                "Descripcion"=>$descripcion,
//                "IdTipoMaterial"=>1,
//            ]);
//        }
        return $material;
    }
    private function getCamion($economico){
        $camion = Camion::where('Economico', $economico)
            ->first();
        return $camion;
    }

    private function getViajeNeto($code,$datos = null){
        $codigo_repetido = $this->getCodigoRepetido($code);
        if($code && !$codigo_repetido){

            $viaje_neto = InicioCamion::where('Code', '=', $code)->first();
            if(!$viaje_neto && $datos){
                $camion = Camion::where('economico', $datos->camion)->first();
                $viaje_neto = InicioCamion::where('IdCamion', $camion ? $camion->IdCamion : null)->where('FechaLlegada', $datos->fecha_llegada->toDateString())->where('HoraLlegada', $datos->hora_llegada->toTimeString())->first();
            }
        }else{
            $camion = Camion::where('economico', $datos->camion)->first();
            $viaje_neto = InicioCamion::where('IdCamion', $camion ? $camion->IdCamion : null)->where('FechaLlegada', $datos->fecha_llegada->toDateString())->where('HoraLlegada', $datos->hora_llegada->toTimeString())->first();

        }
        if(!$viaje_neto){
            $viaje_neto = $this->procesoCompletoViajeNetoManual($datos);
        }else{
            //DD("ENCONTRADO");
            $viaje_neto = $this->procesoCompletoViajeNetoEncontrado($viaje_neto, $datos);
        }
        return $viaje_neto;
    }
    private function getCodigoRepetido($code){
        $coincidencias = InicioCamion::where('Code', '=', $code)->get();
        if(count($coincidencias)>1){
            return TRUE;
        }ELSE{
            return FALSE;
        }
    }
    private function procesoCompletoViajeNetoEncontrado($viaje_neto, $datos_viaje){
        $modificado = $this->viajeModificado($viaje_neto, $datos_viaje);
        $validado = ($viaje_neto->viaje)?TRUE:FALSE;
        $rechazado = ($viaje_neto->viaje_rechazado)?TRUE:FALSE;

        IF($validado){
            if($viaje_neto->viaje->conciliacionDetalles){
                $detalle_conciliacion =  $viaje_neto->viaje->conciliacionDetalles()->where('estado', 1)->where('idconciliacion',$this->conciliacion->idconciliacion)->first();

                if($detalle_conciliacion){
                    $c = $detalle_conciliacion->conciliacion;
                }else{
                    $c = NULL;
                    //$oc = NULL;
                }
                //sdd($c,$detalle_conciliacion,$this->conciliacion->idconciliacion );->where('estado', 1)
                $detalle_otra_conciliacion = $viaje_neto->viaje->conciliacionDetalles()->where('estado', 1)->where('idconciliacion','<>',$this->conciliacion->idconciliacion)->first();
                //dd($detalle_otra_conciliacion);
                if($detalle_otra_conciliacion){
                    $oc = $detalle_otra_conciliacion->conciliacion;
                }else{
                    $oc = NULL;
                }

            }ELSE{
                $c = null;
                $oc = null;
            }

            $conciliado_esta_conciliacion = ($c)?TRUE:FALSE;
            $conciliado_otra_conciliacion = ($oc)?TRUE:FALSE;

            //dd($conciliado_esta_conciliacion,$conciliado_otra_conciliacion,$c,$oc);
//            IF($c){
//
//            }
//            $conciliado_esta_conciliacion = ($c->idconciliacion == $this->conciliacion->idconciliacion)?TRUE:FALSE;
            $pendiente_autorizar_manual = FALSE;
            $no_autorizado_manual = FALSE;
        }ELSE{
            $conciliado = FALSE;
            $conciliado_otra_conciliacion = FALSE;
            $conciliado_esta_conciliacion = FALSE;
            $pendiente_autorizar_manual = ($viaje_neto->Estatus == 29)?TRUE:FALSE;
            $no_autorizado_manual = ($viaje_neto->Estatus == 22)?TRUE:FALSE;
        }
        //dd($conciliado_esta_conciliacion, $viaje_neto);

        // dd($modificado, $validado, $rechazado,$conciliado_otra_conciliacion, $conciliado_esta_conciliacion);
        if($rechazado){
            $ea = $viaje_neto->Estatus;
            $viaje_neto->estatus = $ea - 1 ;
            $viaje_neto->save();
            $viaje_neto->viaje_rechazado->delete();
        }
        if($modificado && $conciliado_otra_conciliacion){
            $detalle_no_conciliado = [
                'idconciliacion' => $this->conciliacion->idconciliacion,
                'idviaje_neto'=>$viaje_neto->IdViajeNeto,
                'idmotivo'=>6,
                'detalle'=>"Este viaje TIENE MODIFICACIONES y ha sido presentado en la conciliación previa: Folio " . $oc->idconciliacion . " Empresa:" . $oc->empresa . " Sindicato: " . $oc->sindicato . ". Dado lo anterior no procede en esta conciliación.",
                'detalle_alert'=>"Error al revertir automáticamente el rechazo el viaje",
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $viaje_neto->code,
                'registro'=>auth()->user()->idusuario,
            ];
            $this->registraDetalleNoConciliado($detalle_no_conciliado);
        }ELSE{

            if($pendiente_autorizar_manual){
                $viaje_neto = $this->autorizaViajeManual($viaje_neto);
            }
            if($no_autorizado_manual){
                $viaje_neto = $this->autorizaViajeManualRechazadoPreviamente($viaje_neto);
            }
            if($rechazado){
                $viaje_neto = $this->validaViajeNetoRechazadoPreviamente($viaje_neto);
                //$this->validaViaje($viaje_neto);
            }
            //dd($validado,$modificado,$conciliado_esta_conciliacion);
            if(!$validado && !$modificado && !$conciliado_esta_conciliacion){
                $this->preparaCatalogos($datos_viaje);
                if($this->validaViaje($viaje_neto, $datos_viaje->tarifa)){
                    $this->conciliaViaje($viaje_neto);
                }

            }else if(!$validado && !$modificado && $conciliado_esta_conciliacion){
                //no debe darse el caso
            }else if(!$validado && $modificado && !$conciliado_esta_conciliacion){
                $this->modificarViaje($viaje_neto, $datos_viaje);
                $this->preparaCatalogos($datos_viaje);
                if($this->validaViaje($viaje_neto, $datos_viaje->tarifa)){
                    $this->conciliaViaje($viaje_neto);
                }
            }else if(!$validado && $modificado && $conciliado_esta_conciliacion){
                //no debe darse este caso
            }else if($validado && !$modificado && !$conciliado_esta_conciliacion){
                if($viaje_neto->viaje){
                    $this->conciliaViaje($viaje_neto);
                }
            }else if($validado && !$modificado && $conciliado_esta_conciliacion){
            }else if($validado && $modificado && !$conciliado_esta_conciliacion){
                //dd($viaje_neto->viaje,$viaje_neto,$datos_viaje);
                $this->preparaCatalogos($datos_viaje);
                $this->modificarViajeValidado($viaje_neto, $datos_viaje);
                if($this->validaViaje($viaje_neto, $datos_viaje->tarifa)){
                    $this->conciliaViaje($viaje_neto);
                }
            }else if($validado && $modificado && $conciliado_esta_conciliacion){
                $this->preparaCatalogos($datos_viaje);
                $this->modificarViajeValidado($viaje_neto, $datos_viaje);
                if($this->validaViaje($viaje_neto, $datos_viaje->tarifa)){
                    $this->conciliaViaje($viaje_neto);
                }
            }
        }
    }
    private function validaViajeNetoRechazadoPreviamente(ViajeNeto $viaje_neto){
        DB::connection('sca')->beginTransaction();
        /**when 1 then set EstatusNuevoViajeNeto=0;
        when 11 then set EstatusNuevoViajeNeto=10;
        when 21 then set EstatusNuevoViajeNeto=20;*/

        $viaje_neto->viaje_rechazado->delete();
        if(!$viaje_neto->viaje_rechazado){
            if($viaje_neto->Estatus >=20 && $viaje_neto->Estatus <=29){
                $viaje_neto->Estatus = 20;

            }else if($viaje_neto->Estatus >= 10 && $viaje_neto->Estatus <= 19){
                $viaje_neto->Estatus = 20;
            }else if($viaje_neto->Estatus >= 0 && $viaje_neto->Estatus <= 9){
                $viaje_neto->Estatus = 0;
            }
            $viaje_neto->Observaciones = $viaje_neto->Observaciones." | Se revirtio el rechazo del viaje automáticamente desde procesamiento de conciliación";
            $viaje_neto->save();
            DB::connection('sca')->commit();
        }else{
            $detalle_no_conciliado = [
                'idconciliacion' => $this->conciliacion->idconciliacion,
                'idviaje_neto'=>$viaje_neto->IdViajeNeto,
                'idmotivo'=>6,
                'detalle'=>"Error al revertir automáticamente el rechazo el viaje",
                'detalle_alert'=>"Error al revertir automáticamente el rechazo el viaje",
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $viaje_neto->Code,
                'registro'=>auth()->user()->idusuario,
            ];
            $this->registraDetalleNoConciliado($detalle_no_conciliado);
            DB::connection('sca')->rollback();
        }
        return $viaje_neto;
    }
    private function autorizaViajeManual($viaje_neto){
        $viaje_neto->Estatus = 20;
        $viaje_neto->Aprobo =  auth()->user()->idusuario;
        $viaje_neto->FechaHoraAprobacion = date("Y-m-d h:i:s");
        $viaje_neto->Observaciones = $viaje_neto->Observaciones." | Viaje autorizado automáticamente desde procesamiento de conciliación";
        $viaje_neto->save();
        return $viaje_neto;
    }
    private function autorizaViajeManualRechazadoPreviamente($viaje_neto){
        $viaje_neto->Estatus = 20;
        $viaje_neto->Aprobo =  auth()->user()->idusuario;
        $viaje_neto->FechaHoraAprobacion = date("Y-m-d h:i:s");
        $viaje_neto->Observaciones = $viaje_neto->Observaciones." | Viaje no autorizado anteriormente autorizado automáticamente desde procesamiento de conciliación";
        $viaje_neto->save();
        return $viaje_neto;
    }
    private function procesoCompletoViajeNetoManual($datos_viaje){
        $this->preparaCatalogos($datos_viaje);
        $viaje_neto = $this->registraViajeNeto($datos_viaje);
        //dd($viaje_neto);
        if($viaje_neto){
            if($this->validaViaje($viaje_neto)){
                $this->conciliaViaje($viaje_neto);
            }
        }
        return $viaje_neto;
    }
    private function preparaCatalogos($viaje){
        $origen = $this->getOrigen($viaje->origen);
        $tiro = $this->getTiro($viaje->tiro);
        $ruta = $this->getRuta($origen, $tiro);
        $this->getCronometria($ruta);
        $this->getMaterial($viaje->material);
        $this->getCamion($viaje->camion);
    }

    private function evalua_viaje($code, InicioViaje $viaje = null, InicioCamion $viaje_neto = null, $complemento_detalle = null){
        if($code){
            $viaje_neto = InicioCamion::where('code', '=', $code)->first();
            if($viaje_neto){
                $viaje_rechazado = $viaje_neto->viaje_rechazado;
                $viaje_validado = $viaje_neto->viaje;
                $viaje_conflicto_pagable = $viaje_neto->conflicto_pagable;
            }else{
                $viaje_rechazado = null;
                $viaje_validado = null;
            }
            //dd($viaje_rechazado, $viaje_neto);
            //$viaje_validado = Viaje::where('code', '=', $code)->first();
            $viaje_pendiente_conciliar = InicioViaje::porConciliar()->where('code', '=', $code)->first();
        }else if($viaje_neto){
            $viaje_neto = $viaje_neto;
            $viaje_conflicto_pagable = $viaje_neto->conflicto_pagable;
            $viaje_validado = $viaje_neto->viaje;
            $viaje_rechazado = $viaje_neto->viaje_rechazado;
            $viaje_pendiente_conciliar =  InicioViaje::porConciliar()->where('inicio_viajes.IdInicioViajes', '=', $viaje_neto->IdViaje)->first();

        }else if($viaje){
            $viaje_neto = $viaje->inicio_camion;

            $viaje_conflicto_pagable = $viaje_neto->conflicto_pagable;
            $viaje_rechazado = $viaje->inicio_camion->viaje_rechazado;
            $viaje_validado = $viaje;
            $viaje_pendiente_conciliar =  InicioViaje::porConciliar()->where('inicio_viajes.IdInicioViajes', '=', $viaje->IdInicioViajes)->first();
            // dd($viaje_neto,$viaje_validado,$viaje_pendiente_conciliar);
        }
        $id_conciliacion = $this->conciliacion->idconciliacion;
        if (!$viaje_neto) {
            $detalle_no_conciliado = [
                'idconciliacion' => $id_conciliacion,
                'idmotivo'=>3,
                'detalle'=>"Viaje no encontrado en sistema. Favor de presentarlo a aclaración en caso de que sea procedente.".$complemento_detalle,
                'detalle_alert'=>"<span style='text-align:left'>Viaje no encontrado en sistema. <br/>Favor de presentarlo a aclaración en caso de que sea procedente.</span>".$complemento_detalle,
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $code,
                'registro'=>auth()->user()->idusuario,
            ];
            $evaluacion["detalle"] = FALSE;
            $evaluacion["detalle_nc"] = $detalle_no_conciliado;

        }
        else if ($viaje_neto && !$viaje_validado && !$viaje_rechazado && $viaje_neto->Estatus == 29 && $viaje_neto->Estatus != 22) {
           // dd($viaje_neto);
            $detalle_no_conciliado = [
                'idconciliacion' => $id_conciliacion,
                'idinicioviaje'=>$viaje_neto->id,
                'idmotivo'=>2,
                'detalle'=>"Viaje con conflicto de validación, pasar a mesa de aclaraciones para revisión. ". $complemento_detalle,
                'detalle_alert'=>"Viaje con conflicto de validación, pasar a mesa de aclaraciones para revisión.". $complemento_detalle,
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $viaje_neto->code,
                'registro'=>auth()->user()->idusuario,
            ];
            $evaluacion["detalle"] = FALSE;
            $evaluacion["detalle_nc"] = $detalle_no_conciliado;
        }
        else if ($viaje_neto && !$viaje_validado && $viaje_rechazado && $viaje_neto->Estatus != 29 && $viaje_neto->Estatus != 22) {
            $detalle_no_conciliado = [
                'idconciliacion' => $id_conciliacion,
                'idinicioviaje'=>$viaje_neto->id,
                'idmotivo'=>8,
                'detalle'=>"Viaje rechazado en proceso de validación. En caso de tener duda favor de presentarse a la mesa de aclaraciones. ". $complemento_detalle,
                'detalle_alert'=>"<span style='text-align:left'>Viaje rechazado en proceso de validación.<br/><br/>En caso de tener duda favor de presentarlo a la mesa de aclaraciones.</span>",
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $viaje_neto->code,
                'registro'=>auth()->user()->idusuario,
            ];
            $evaluacion["detalle"] = FALSE;
            $evaluacion["detalle_nc"] = $detalle_no_conciliado;
        }
        else if ($viaje_neto && !$viaje_validado && !$viaje_rechazado && $viaje_neto->Estatus != 29 && $viaje_neto->Estatus == 22) {
            $detalle_no_conciliado = [
                'idconciliacion' => $id_conciliacion,
                'idinicioviaje'=>$viaje_neto->id,
                'idmotivo'=>9,
                'detalle'=>"Viaje manual ingresado no autorizado. En caso de tener duda favor de presentarlo a la mesa de aclaraciones. ".' '. $complemento_detalle,
                'detalle_alert'=>"<span style='text-align:left'>Viaje manual ingresado no autorizado.<br/><br/>En caso de tener duda favor de presentarlo a la mesa de alcaraciones.</span>",
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $viaje_neto->code,
                'registro'=>auth()->user()->idusuario,
            ];
            $evaluacion["detalle"] = FALSE;
            $evaluacion["detalle_nc"] = $detalle_no_conciliado;
        }

        else if ($viaje_neto && !$viaje_validado && !$viaje_rechazado && $viaje_neto->Estatus != 29 && $viaje_neto->Estatus != 22 ) {
            $detalle_no_conciliado = [
                'idconciliacion' => $id_conciliacion,
                'idinicioviaje'=>$viaje_neto->id,
                'idmotivo'=>2,
                'detalle'=>"Viaje con conflicto de validación, pasar a mesa de aclaraciones para revisión. ". $complemento_detalle,
                'detalle_alert'=>"Viaje con conflicto de validación, pasar a mesa de aclaraciones para revisión.". $complemento_detalle,
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $viaje_neto->code,
                'registro'=>auth()->user()->idusuario,
            ];
            $evaluacion["detalle"] = FALSE;
            $evaluacion["detalle_nc"] = $detalle_no_conciliado;
        }
        else if($viaje_neto->en_conflicto_tiempo && !$viaje_conflicto_pagable){
            $detalle_no_conciliado = [
                'idconciliacion' => $id_conciliacion,
                'idinicioviaje'=>$viaje_neto->id,
                'idmotivo'=>1,
                'detalle'=>"".$viaje_neto->descripcion_conflicto,
                'detalle_alert'=>"".$viaje_neto->descripcion_conflicto_alert,
                'timestamp'=>Carbon::now()->toDateTimeString(),
                'Code' => $viaje_neto->code,
                'registro'=>auth()->user()->idusuario,
            ];
            $evaluacion["detalle"] = FALSE;
            $evaluacion["detalle_nc"] = $detalle_no_conciliado;
        }
        else if ($viaje_pendiente_conciliar) {

            if ($viaje_pendiente_conciliar->disponible()) {
                $detalle = [
                    'idconciliacion' => $id_conciliacion,
                    'idinicioviaje' => $viaje_neto->id,
                    'idviaje' => $viaje_pendiente_conciliar->IdInicioViajes,
                    'Code' => $code,
                    'timestamp' => Carbon::now()->toDateTimeString(),
                    'estado' => 1,
                    'registro'=>auth()->user()->idusuario,
                ];
                $evaluacion["detalle"] = $detalle;
                $evaluacion["detalle_nc"] = FALSE;
            } else {
                $cd = $viaje_validado->conciliacionDetalles->where('estado', 1)->first();
                $c = $cd->conciliacion;
                if($c->idconciliacion == $id_conciliacion) {
                    $detalle_no_conciliado = [
                        'idconciliacion' => $id_conciliacion,
                        'idinicioviaje'=>$viaje_neto->id,
                        'idmotivo'=>2,
                        'detalle'=>"Viaje con conflicto de validación, pasar a mesa de aclaraciones para revisión. ". $complemento_detalle,
                        'detalle_alert'=>"Viaje con conflicto de validación, pasar a mesa de aclaraciones para revisión.". $complemento_detalle,
                        'timestamp'=>Carbon::now()->toDateTimeString(),
                        'Code' => $viaje_neto->code,
                        'registro'=>auth()->user()->idusuario,
                    ];
                    $evaluacion["detalle"] = FALSE;
                    $evaluacion["detalle_nc"] = $detalle_no_conciliado;
                } else {
                    $detalle_no_conciliado = [
                        'idconciliacion' => $id_conciliacion,
                        'idinicioviaje'=>$viaje_neto->id,
                        'idviaje' => $viaje_pendiente_conciliar->IdInicioViajes,
                        'idmotivo'=>5,
                        'detalle'=>"Este viaje ya ha sido presentado en la conciliación previa: Folio " . $cd->idconciliacion . " Empresa:" . $c->empresa . " Sindicato: " . $c->sindicato . ". Dado lo anterior no procede en esta conciliación.",
                        'detalle_alert'=>"<span style='text-align:left'><strong>Este viaje ya ha sido presentado en la conciliación previa:</strong> <br/><br/> "
                            . "<ul><li> Folio: " . $cd->idconciliacion . "</li><li> Empresa: " . $c->empresa . "</li><li> Sindicato: " . $c->sindicato . ". </li> <br/>Dado  lo anterior <strong>no procede</strong> en esta conciliación.</span>",
                        'Code' => $code,
                        'registro'=>auth()->user()->idusuario,
                    ];
                    $evaluacion["detalle"] = FALSE;
                    $evaluacion["detalle_nc"] = $detalle_no_conciliado;
                }
            }
        } else {
            $c = $viaje_validado->conciliacionDetalles->where('estado', 1)->first()->conciliacion;
            if($c->idconciliacion == $id_conciliacion) {
                $detalle_no_conciliado = [
                    'idconciliacion' => $id_conciliacion,
                    'idinicioviaje'=>$viaje_neto->id,
                    'idmotivo'=>7,
                    'detalle'=>"Viaje conciliado en esta conciliación.",
                    'detalle_alert'=>"Viaje conciliado en esta conciliación.",
                    'timestamp'=>Carbon::now()->toDateTimeString(),
                    'Code' => $viaje_neto->code,
                    'registro'=>auth()->user()->idusuario,
                ];
                $evaluacion["detalle"] = FALSE;
                $evaluacion["detalle_nc"] = $detalle_no_conciliado;
            } else {
                $detalle_no_conciliado = [
                    'idconciliacion' => $id_conciliacion,
                    'idinicioviaje'=>$viaje_neto->id,
                    'idviaje' => $viaje_validado->IdInicioViajes,
                    'idmotivo'=>5,
                    'timestamp'=>Carbon::now()->toDateTimeString(),
                    'detalle'=>"Este viaje ya ha sido presentado en la conciliación previa: Folio: " . $c->idconciliacion . " Empresa: " . $c->empresa . " Sindicato: " . $c->sindicato . ". Dado  lo anterior no procede en esta conciliación.",
                    'detalle_alert'=>"<span style='text-align:left'><strong>Este viaje ya ha sido presentado en la conciliación previa:</strong> <br/><br/> "
                        . "<ul><li> Folio: " . $c->idconciliacion . "</li><li> Empresa: " . $c->empresa . "</li><li> Sindicato: " . $c->sindicato . ". </li> <br/>Dado  lo anterior <strong>no procede</strong> en esta conciliación.</span>",
                    'Code' => $code,
                    'registro'=>auth()->user()->idusuario,
                ];
                $evaluacion["detalle"] = FALSE;
                $evaluacion["detalle_nc"] = $detalle_no_conciliado;
            }
        }
        return $evaluacion;
    }
    private function registraDetalleNoConciliado($datos_detalle){
        //dd($datos_detalle);
        $detalle_no_conciliado = ConciliacionSuministroDetalleNoConciliado::create($datos_detalle);
        return $detalle_no_conciliado;
    }
    private function registraDetalle($array){
        $detalle = ConciliacionSuministroDetalle::create($array);
        return $detalle;
    }
}