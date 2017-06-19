<?php
/**
 * Created by PhpStorm.
 * User: DBENITEZ
 * Date: 16/06/2017
 * Time: 06:30 PM
 */

namespace App\Models\Transformers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Themsaid\Transformers\AbstractTransformer;

class ConciliacionesDetalladoReporteTransformer
{

    protected  $dato;

    public static function toArray(Request $request, $horaInicial, $horaFinal, $codigo)
    {

        $timestamp_inicial = $request->get('FechaInicial') . ' ' . $horaInicial;
        $timestamp_final = $request->get('FechaFinal') . ' ' . $horaFinal;

        if($request->get('Codigo') != " "){
            $dato = "where  conciliacion.idconciliacion = '{$codigo}'";
        }else{
            $dato = "where   conciliacion.fecha_conciliacion between '{$timestamp_inicial}' and '{$timestamp_final}'";
        }

        $SQL = "SELECT conciliacion.idconciliacion AS folio_conciliacion,
       conciliacion.Folio AS folio_conciliacion_historico,
       conciliacion.fecha_conciliacion,
       conciliacion.`timestamp` AS fecha_registro_conciliacion,
       empresas.razonSocial AS empresa,
       sindicatos.Descripcion AS sindicato,
       viajes.code,
       viajes.FechaCarga AS fecha_carga_viaje,
       viajes.HoraCarga AS hora_carga_viaje,
       viajes.FechaSalida AS fecha_salida_viaje,
       viajes.HoraSalida AS hora_salida_viaje,
       viajes.FechaLlegada AS fecha_llegada,
       viajes.HoraLlegada AS hora_llegada,
       camiones.Economico AS camion,
       viajes.CubicacionCamion AS cubicacion_camion_viaje,
       camiones.CubicacionParaPago AS cubicacion_camion,
       viajes.Importe AS importe_viaje,
       IF(
          conciliacion.estado < 0,
          'CALCELADA',
          IF(
             conciliacion.estado = 0,
             'GENERADA',
             IF(conciliacion.estado = 1,
                'CERRADA',
                IF(conciliacion.estado = 2, 'APROBADA', ''))))
          AS estado_conciliacion,
       viajes.IdViaje
  FROM ((((viajes viajes
           LEFT OUTER JOIN camiones camiones
              ON (viajes.IdCamion = camiones.IdCamion))
          INNER JOIN
          conciliacion_detalle conciliacion_detalle
             ON (conciliacion_detalle.idviaje = viajes.IdViaje))
         LEFT OUTER JOIN
         conciliacion conciliacion
            ON (conciliacion_detalle.idconciliacion =
                   conciliacion.idconciliacion))
        LEFT OUTER JOIN sindicatos sindicatos
           ON (conciliacion.idsindicato = sindicatos.IdSindicato))
       LEFT OUTER JOIN empresas empresas
          ON (conciliacion.idempresa = empresas.IdEmpresa)
        ".$dato;

        return DB::connection('sca')->select(DB::raw($SQL));
    }
}