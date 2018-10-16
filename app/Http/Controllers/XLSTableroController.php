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

        $novalidados = DB::connection("sca")->table("viajesnetos as vn")
            ->selectRaw("vn.IdCamion, vn.idorigen, vn.FechaSalida AS fs, vn.HoraSalida AS hs, vn.CubicacionCamion AS cubicacion, vn.IdTiro, vn.FechaLlegada AS fl, 
                            vn.HoraLlegada AS hl, vn.IdMaterial,
                            vn.Code AS code, vn.folioMina AS foliomina, vn.folioSeguimiento AS folioseg, 
                            IF(vn.FechaLlegada >= '".$dosSemanas."','0','1') AS alerta,
                            IF(vn.estatus = 29, 'Viaje Manual - Pendiente de Autorizar',
                            IF(vn.estatus = 20, 'Viaje Manual - Pendiente de Validar',
                            IF(vn.estatus = 0, 'Viaje - Pendiente por Validar',''))) AS estatus,
                            IF(vn.denegado = 1, 'DENEGADO', '') AS denegado,
                        c.Economico AS economico, o.Descripcion AS origen, t.Descripcion AS tiro, m.Descripcion AS material")
            ->leftjoin("viajes as v","vn.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->join("camiones as c", "c.IdCamion", "=", "vn.IdCamion")
            ->join("origenes as o", "o.IdOrigen","=","vn.IdOrigen")
            ->join("tiros as t","t.IdTiro", "=", "vn.IdTiro")
            ->join("materiales as m","m.IdMaterial","=", "vn.IdMaterial")
            ->whereNull("v.IdViajeNeto")->whereNull("vr.IdViajeRechazado")
            ->whereRaw("vn.FechaLlegada <= '".$inicioFecha."'")
            ->orderBy("vn.FechaLlegada","desc")->get();

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

        $validados = DB::connection("sca")->table("viajes as va")
            ->selectRaw("v.IdCamion,  v.idorigen,  v.FechaSalida AS fs, v.HoraSalida AS hs,
                            v.CubicacionCamion AS cubicacion, v.IdTiro,  v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                            v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, 
                            IF(v.FechaLlegada >= '".$dosSemanas."','0','1') AS alerta,
                            IF(v.estatus = 29, 'Viaje Manual - Cargado',
                            IF(v.estatus = 20, 'Viaje Manual - Pendiente Validar',
                            IF(v.estatus = 0, 'Viaje - Pendiente por Validar',
                            IF(v.estatus = 1, 'Viaje - Validado', 
                            IF(v.estatus = 21, 'Validado',''))))) AS estatus,
                            IF(v.denegado = 1, 'DENEGADO', '') AS denegado,
                            c.Economico AS economico, o.Descripcion AS origen, t.Descripcion AS tiro, m.Descripcion AS material")
            ->leftjoin(DB::raw('(select idviaje_neto from conciliacion_detalle where estado =1) cd'),
                function($join)
                {
                    $join->on('va.IdViajeNeto', '=', 'cd.idviaje_neto');
                })
            ->join("viajesnetos as v", "va.IdViajeNeto", "=", "v.IdViajeNeto")
            ->join("camiones as c", "c.IdCamion", "=", "v.IdCamion")
            ->join("origenes as o", "o.IdOrigen","=","v.IdOrigen")
            ->join("tiros as t","t.IdTiro", "=", "v.IdTiro")
            ->join("materiales as m","m.IdMaterial","=", "v.IdMaterial")
            ->whereNull("cd.idviaje_neto")
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
