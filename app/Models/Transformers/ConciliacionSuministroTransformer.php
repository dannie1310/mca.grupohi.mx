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
            COUNT(ic.id) AS num,
            cd.idconciliacion_detalle,
            cd.idinicioviaje,
            ic.code,
            GROUP_CONCAT(c.idconciliacion)
        FROM
            ((conciliacion_suministro_detalle cd
            INNER JOIN inicio_camion ic ON (cd.idinicioviaje = ic.id)
                AND (ic.id = cd.idinicioviaje))
            INNER JOIN conciliacion_suministro c ON (cd.idconciliacion = c.idconciliacion)
                AND (c.idconciliacion = cd.idconciliacion))
                INNER JOIN
            inicio_viajes iv ON (iv.IdInicioCamion = ic.id)
        WHERE
            cd.estado = 1
                AND c.idconciliacion = '{$conciliacion->idconciliacion}'
        GROUP BY cd.idinicioviaje , ic.Code
        HAVING COUNT(ic.id) > 1";

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
