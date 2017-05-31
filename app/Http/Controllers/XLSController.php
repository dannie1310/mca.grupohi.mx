<?php

namespace App\Http\Controllers;

use App\Models\Conciliacion\Conciliacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Laracasts\Flash\Flash;
use App\Models\Conciliacion\ConciliacionDetalle;
use Carbon\Carbon;

class XLSController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:descargar-excel-conciliacion', ['only' => ['conciliacion']]);

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function conciliacion($id)
    {
        $conciliacion = Conciliacion::findOrFail($id);


        $now = Carbon::now();
        Excel::create('Conciliacion_'.$conciliacion->idconciliacion.'_'.$now->format("Y-m-d")."__".$now->format("h:i:s"), function($excel) use($conciliacion) {
            $excel->sheet('Portada', function($sheet) use($conciliacion) {
                $sheet->row(1, array(
                    'Folio Global','Fecha Histórico','Folio Histórico', 'Rango de Fechas', 'Empresa', 'Sindicato', ('Número de Viajes')
                    ,'Volumen Conciliado'
                    ,'Volumen Pagado'
                    ,'Importe Conciliado'
                    ,'Importe Pagado'
                    
                ));
                $sheet->row(2, array(
                    $conciliacion->idconciliacion,
                    $conciliacion->fecha_conciliacion->format("d-m-Y"),
                    $conciliacion->Folio, 
                    $conciliacion->rango, 
                    ($conciliacion->empresa)?$conciliacion->empresa->razonSocial:'', 
                    ($conciliacion->sindicato)?$conciliacion->sindicato->Descripcion:'', count($conciliacion->viajes())
                    , $conciliacion->volumen_f
                    , $conciliacion->volumen_pagado_alert
                    , $conciliacion->importe_f
                    , $conciliacion->importe_pagado_alert
                ));
                
                $sheet->row(4, array(
                    'Cantidad Viajes Manuales',
                    'Importe Viajes Manuales',
                    'Volúmen Viajes Manuales',
                    'Porcentaje Viajes Manuales'
                ));
                
                $sheet->row(5, array(
                    count($conciliacion->viajes_manuales()),
                    $conciliacion->importe_viajes_manuales_f,
                    $conciliacion->volumen_viajes_manuales_f,
                    $conciliacion->porcentaje_importe_viajes_manuales
                ));

            });

            $excel->sheet('Detalle', function($sheet) use($conciliacion) {
                $sheet->row(1, array(
                    'Camión','Ticket','Registró', 'Fecha y Hora Llegada', 'Material', 'Cubicacion', 'Importe', 'Tipo'
                ));
                $i = 2;
                foreach($conciliacion->conciliacionDetalles as $detalle){
                    if($detalle->estado >=0){
                        $sheet->row($i, array(
                            $detalle->viaje->camion->Economico,
                            $detalle->viaje->code,
                            $detalle->usuario_registro,
                            $detalle->viaje->FechaLlegada.' '.$detalle->viaje->HoraLlegada,
                            $detalle->viaje->material->Descripcion,
                            $detalle->viaje->CubicacionCamion,
                            $detalle->viaje->Importe,
                            $detalle->viaje->tipo
                        ));
                        $i++;
                    }
                    
                }
                
            });

        })->export('xlsx');
        
    }

    public function conciliaciones()
    {
        $conciliaciones = DB::connection('sca')->table('conciliacion')
            ->leftJoin('empresas', 'conciliacion.idempresa', '=', 'empresas.IdEmpresa')
            ->leftJoin('sindicatos', 'conciliacion.idsindicato', '=', 'sindicatos.IdSindicato')
            ->select(
                "conciliacion.*",
                "empresas.RazonSocial as empresa",
                "sindicatos.Descripcion as sindicato",
                DB::raw("(SELECT count(idconciliacion_detalle) FROM conciliacion_detalle where idconciliacion = conciliacion.idconciliacion) as num_viajes"),
                DB::raw("(select sum(CubicacionCamion) "
                    . "from conciliacion as conc_vol "
                    . "left join conciliacion_detalle on conc_vol.idconciliacion = conciliacion_detalle.idconciliacion "
                    . "left join viajes on conciliacion_detalle.idviaje = viajes.IdViaje where conc_vol.idconciliacion = conciliacion.idconciliacion "
                    . "and conciliacion_detalle.estado = 1 "
                    . "group by conc_vol.idconciliacion limit 1) as volumen"),
                DB::raw("IF(DATE_FORMAT(conciliacion.fecha_conciliacion, '%Y%m%d') <= ".Conciliacion::FECHA_HISTORICO." AND conciliacion.VolumenPagado > 0, 'Pendiente' , conciliacion.VolumenPagado) as volumen_pagado_alert"),
                DB::raw("(select sum(Importe) "
                    . "from conciliacion as conc_imp "
                    . "left join conciliacion_detalle on conc_imp.idconciliacion = conciliacion_detalle.idconciliacion "
                    . "left join viajes on conciliacion_detalle.idviaje = viajes.IdViaje where conc_imp.idconciliacion = conciliacion.idconciliacion "
                    . "and conciliacion_detalle.estado = 1 "
                    . "group by conc_imp.idconciliacion limit 1) as importe"),
                DB::raw("IF(DATE_FORMAT(conciliacion.fecha_conciliacion, '%Y%m%d') <= ".Conciliacion::FECHA_HISTORICO." AND conciliacion.ImportePagado > 0, 'Pendiente' , conciliacion.ImportePagado) as importe_pagado_alert"),
                DB::raw("IF(conciliacion.estado = 0, 'Generada', IF(conciliacion.estado = 1, 'Cerrada', IF(conciliacion.estado = 2, 'Aprobada', IF(conciliacion.estado < 0, 'Calcelada', '')))) as estado_str")
            )->get();

        $now = Carbon::now();
        Excel::create('Conciliaciones'.'_'.$now->format("Y-m-d")."__".$now->format("h:i:s"), function($excel) use($conciliaciones) {
            $excel->sheet('Conciliaciones', function($sheet) use($conciliaciones) {
                $sheet->row(1, array(
                    'Folio Global','Folio','Fecha','Empresa','Sindicato','Cantidad Viajes','Volumen Conciliado','Volumen Pagado', 'Importe Conciliado','Importe Pagado', 'Estatus'
                ));
                $i = 2;
                foreach($conciliaciones as $conciliacion){
                    $sheet->row($i, array(
                        $conciliacion->idconciliacion,
                        $conciliacion->Folio, 
                        $conciliacion->fecha_conciliacion,
                        $conciliacion->empresa,
                        $conciliacion->sindicato,
                        $conciliacion->num_viajes,
                        $conciliacion->volumen,
                        is_numeric($conciliacion->volumen_pagado_alert) ? $conciliacion->volumen_pagado_alert : $conciliacion->volumen_pagado_alert,
                        $conciliacion->importe,
                        is_numeric($conciliacion->importe_pagado_alert) ? $conciliacion->importe_pagado_alert : $conciliacion->importe_pagado_alert,
                        $conciliacion->estado_str
                    ));
                    $i++;
                }
            });

        })->export('xlsx');
        
        //Excel::load(storage_path('exports/excel/') . $filename)->download('xls');
    }
}
