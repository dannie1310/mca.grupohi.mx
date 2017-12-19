<?php

namespace App\Models\Transformers;

use Illuminate\Database\Eloquent\Model;
use App\Models\ConciliacionSuministro\ConciliacionSuministroDetalle;
use App\User;
use Themsaid\Transformers\AbstractTransformer;
use App\Models\ConciliacionSuministro\ConciliacionSuministroDetalleNoConciliado;
use Illuminate\Support\Facades\DB;

class ConciliacionSuministroTransformer extends AbstractTransformer
{
    public function transformModel(Model $conciliacion)
    {
        $duplicidad = "SELECT 
            COUNT(idinicioviaje) AS num,
            conciliacion_detalle.idconciliacion_detalle,
            conciliacion_detalle.idinicioviaje,
            viajesnetos.code,
            GROUP_CONCAT(conciliacion.idconciliacion)
        FROM
            ((conciliacion_suministro_detalle conciliacion_detalle
            INNER JOIN inicio_camion viajesnetos ON (conciliacion_detalle.idinicioviaje = viajesnetos.id)
                AND (viajesnetos.id = conciliacion_detalle.idinicioviaje))
            INNER JOIN conciliacion_suministro conciliacion ON (conciliacion_detalle.idconciliacion = conciliacion.idconciliacion)
                AND (conciliacion.idconciliacion = conciliacion_detalle.idconciliacion))
                INNER JOIN
            inicio_viajes viajes ON (viajes.IdInicioCamion = viajesnetos.id)
        WHERE
            conciliacion_detalle.estado = 1
                AND conciliacion.idconciliacion = '{$conciliacion->idconciliacion}'
        GROUP BY conciliacion_detalle.idinicioviaje , viajesnetos.Code
        HAVING COUNT(idinicioviaje) > 1";

        $datos = DB::connection('sca')->select(DB::raw($duplicidad));

        $num = "";
        $code = "";
        $duplicados = [];
        $i = 0;
        foreach ($datos as $d) {
            $duplicados[$i] = [
                'numduplicado' => $d->num,
                'codeduplicado' => $d->code
            ];
            $i++;

        }

        $output = [
            'id' => $conciliacion->idconciliacion,
            'num_viajes' => $conciliacion->conciliacionSuministroDetalles->where('estado', '=', 1)->count(),
            'volumen' => $conciliacion->volumen_f,
            'volumen_sf' => $conciliacion->volumen,
            'volumen_pagado' => $conciliacion->volumen_pagado_f,
            'es_historico' => ($conciliacion->es_historico) ? 1 : 0,
            'volumen_pagado_sf' => $conciliacion->VolumenPagado,

            'detalles' => ConciliacionSuministroDetalleTransformer::transform(ConciliacionSuministroDetalle::where('idconciliacion', $conciliacion->idconciliacion)->get()),
            'detalles_nc' => ConciliacionSuministroDetalleNoConciliadoTransformer::transform(ConciliacionSuministroDetalleNoConciliado::where('idconciliacion', $conciliacion->idconciliacion)->get()),
            'empresa' => $conciliacion->empresa ? $conciliacion->empresa->razonSocial : '',
            'sindicato' => $conciliacion->sindicato ? $conciliacion->sindicato->NombreCorto : '',
            'estado' => $conciliacion->estado,
            'estado_str' => $conciliacion->estado_str,
            'cancelacion' => !$conciliacion->cancelacion ? [] : [
                'motivo' => $conciliacion->cancelacion->motivo,
                'cancelo' => User::find($conciliacion->cancelacion->idcancelo)->present()->nombreCompleto,
                'timestamp' => $conciliacion->cancelacion->timestamp_cancelacion
            ],
            'fecha' => $conciliacion->fecha_conciliacion->format("d-m-Y"),
            'folio' => $conciliacion->Folio,
            'rango' => $conciliacion->rango,
            'volumen_viajes_manuales' => $conciliacion->volumen_viajes_manuales_f,
            'porcentaje_volumen_viajes_manuales' => $conciliacion->porcentaje_volumen_viajes_manuales,
            'volumen_viajes_moviles' => $conciliacion->volumen_viajes_moviles_f,
            'duplicados' => $duplicados
        ];

        return $output;
    }
}
