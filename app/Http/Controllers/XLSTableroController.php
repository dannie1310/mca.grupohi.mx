<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Laracasts\Flash\Flash;
use Carbon\Carbon;

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
        $fecha = date('Y-m-d');
        $inicioFecha = strtotime('-7 day', strtotime($fecha));
        $inicioFecha = date('Y-m-d', $inicioFecha);
        $dosSemanas = strtotime('-7 day', strtotime($inicioFecha));
        $dosSemanas = date('Y-m-d', $dosSemanas);

        $novalidados = DB::connection("sca")->table("viajesnetos as v")
            ->selectRaw("v.IdCamion, c.Economico AS economico, v.idorigen, o.Descripcion AS origen, v.FechaSalida AS fs, v.HoraSalida AS hs,
                    v.CubicacionCamion AS cubicacion, v.IdTiro, t.Descripcion AS tiro, v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                    m.Descripcion AS material, v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, IF(v.FechaLlegada >= '".$dosSemanas."','0','1') AS alerta,
                    IF(v.estatus = 29, 'Viaje Manual - Pendiente de Autorizar',
                    IF(v.estatus = 20, 'Viaje Manual - Pendiente de Validar',
                    IF(v.estatus = 0, 'Viaje - Pendiente por Validar',''))) AS estatus,
                    IF(v.denegado = 1, 'DENEGADO', '') AS denegado")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("camiones as c", "c.IdCamion", "=", "v.IdCamion")
            ->leftjoin("origenes as o", "o.IdOrigen","=","v.IdOrigen")
            ->leftjoin("tiros as t","t.IdTiro", "=", "v.IdTiro")
            ->leftjoin("materiales as m","m.IdMaterial","=", "v.IdMaterial")
            ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")
            ->whereRaw("v.FechaLlegada <= '".$inicioFecha."'")
            ->orderBy("v.FechaLlegada","desc")->get();

        $now = Carbon::now();
        Excel::create('Tablero_viajesnovalidados'.'_'.$now->format("Y-m-d")."__".$now->format("h:i:s"), function($excel) use($novalidados) {
            $excel->sheet('NoValidados', function($sheet) use($novalidados) {
                $sheet->row(1, array(
                    'Economico','Origen','Fecha Salida','Cubicacion','Destino','Fecha Llegada','Material','Ticket','Folio Mina', 'Folio Seguimiento','Alerta', 'Estatus','Estatus Viaje'
                ));
                $i = 2;
                foreach($novalidados as $a){
                    $sheet->row($i, array(
                        $a->economico,
                        $a->origen,
                        $a->fs." ".$a->hs,
                        $a->cubicacion,
                        $a->tiro,
                        $a->fl." ".$a->hl,
                        $a->material,
                        $a->code,
                        $a->foliomina,
                        $a->folioseg,
                        $a->alerta,
                        $a->estatus,
                        $a->denegado
                    ));
                    $i++;
                }
            });
        })->export('xlsx');
    }

    public function validados(){
        $fecha = date('Y-m-d');
        $inicioFecha = strtotime('-7 day', strtotime($fecha));
        $inicioFecha = date('Y-m-d', $inicioFecha);
        $dosSemanas = strtotime('-7 day', strtotime($inicioFecha));
        $dosSemanas = date('Y-m-d', $dosSemanas);

        $validados = DB::connection("sca")->table("viajesnetos as v")
            ->selectRaw("v.IdCamion, c.Economico AS economico, v.idorigen, o.Descripcion AS origen, v.FechaSalida AS fs, v.HoraSalida AS hs,
                    v.CubicacionCamion AS cubicacion, v.IdTiro, t.Descripcion AS tiro, v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                    m.Descripcion AS material, v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, IF(v.FechaLlegada >= '".$dosSemanas."','0','1') AS alerta,
                    IF(v.estatus = 29, 'Viaje Manual - Cargado',
                    IF(v.estatus = 20, 'Viaje Manual - Pendiente Validar',
                    IF(v.estatus = 0, 'Viaje - Pendiente por Validar',
                    IF(v.estatus = 1, 'Viaje - Validado', 
                    IF(v.estatus = 21, 'Validado',''))))) AS estatus,
                    IF(v.denegado = 1, 'DENEGADO', '') AS denegado")
            ->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
            ->join("camiones as c", "c.IdCamion", "=", "v.IdCamion")
            ->join("origenes as o", "o.IdOrigen","=","v.IdOrigen")
            ->join("tiros as t","t.IdTiro", "=", "v.IdTiro")
            ->join("materiales as m","m.IdMaterial","=", "v.IdMaterial")
            ->whereIn("v.Estatus",array('1','21'))
            ->whereNotNull("vr.IdViaje")
            ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")
            ->orderBy("v.FechaLlegada","desc")->get();

        $now = Carbon::now();
        Excel::create('Tablero_viajesvalidados'.'_'.$now->format("Y-m-d")."__".$now->format("h:i:s"), function($excel) use($validados) {
            $excel->sheet('Validados', function($sheet) use($validados) {
                $sheet->row(1, array(
                    'Economico','Origen','Fecha Salida','Cubicacion','Destino','Fecha Llegada','Material','Ticket','Folio Mina', 'Folio Seguimiento','Alerta', 'Estatus','Estatus Viaje'
                ));
                $i = 2;
                foreach($validados as $a){
                    $sheet->row($i, array(
                        $a->economico,
                        $a->origen,
                        $a->fs." ".$a->hs,
                        $a->cubicacion,
                        $a->tiro,
                        $a->fl." ".$a->hl,
                        $a->material,
                        $a->code,
                        $a->foliomina,
                        $a->folioseg,
                        $a->alerta,
                        $a->estatus,
                        $a->denegado
                    ));
                    $i++;
                }
            });
        })->export('xlsx');
    }

}
