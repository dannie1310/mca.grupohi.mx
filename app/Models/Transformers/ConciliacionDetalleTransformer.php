<?php

namespace App\Models\Transformers;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;
use Illuminate\Support\Facades\DB;

class ConciliacionDetalleTransformer extends AbstractTransformer
{

    public function transformModel(Model $detalle) {

        $duplicidad ="SELECT count(idviaje_neto) AS num,
                   conciliacion_detalle.idconciliacion_detalle,
                   conciliacion_detalle.idviaje_neto,
                   viajesnetos.Code,
                   group_concat(conciliacion.idconciliacion)
              FROM ((prod_sca_pista_aeropuerto_2.conciliacion_detalle conciliacion_detalle
                      INNER JOIN prod_sca_pista_aeropuerto_2.viajesnetos viajesnetos
                         ON     (conciliacion_detalle.idviaje_neto =
                                    viajesnetos.IdViajeNeto)
                            AND (viajesnetos.IdViajeNeto =
                                    conciliacion_detalle.idviaje_neto))
                     INNER JOIN prod_sca_pista_aeropuerto_2.conciliacion conciliacion
                        ON     (conciliacion_detalle.idconciliacion =
                                   conciliacion.idconciliacion)
                           AND (conciliacion.idconciliacion =
                                conciliacion_detalle.idconciliacion))
                   INNER JOIN prod_sca_pista_aeropuerto_2.viajes viajes
                      ON (viajes.IdViajeNeto = viajesnetos.IdViajeNeto)
             WHERE   conciliacion_detalle.estado = 1
                   AND conciliacion.idconciliacion = '{$detalle->idconciliacion}'
            GROUP BY conciliacion_detalle.idviaje_neto, viajesnetos.Code
            HAVING count(idviaje_neto) > 1";

        $datos = DB::connection('sca')->select(DB::raw($duplicidad));
        $num ="";
        $code ="";
        foreach ($datos as $d) {
            $num = $d->num;
            $code = $d->Code;
        }

        $output = [
            'idconciliacion_detalle' => $detalle->idconciliacion_detalle,
            'id'                     => ($detalle->viaje_neto->viaje)?$detalle->viaje_neto->viaje->IdViaje:0,
            'timestamp_llegada'      => $detalle->viaje_neto->FechaLlegada.' ('.$detalle->viaje_neto->HoraLlegada.')',
            'cubicacion_camion'      => $detalle->viaje_neto->CubicacionCamion,
            'camion'                 => $detalle->viaje_neto->camion->Economico,
            'material'               => $detalle->viaje_neto->material->Descripcion,
            'importe'                => ($detalle->viaje_neto->viaje)?number_format($detalle->viaje_neto->viaje->Importe, 2, '.', ','):0.00,
            'code'                   => $detalle->viaje_neto->Code,
            'estado'                 => $detalle->estado,
            'cancelacion'            => $detalle->estado == 1 ? [] : [
                'motivo' => $detalle->cancelacion->motivo,
                'cancelo' => User::find($detalle->cancelacion->idcancelo)->present()->nombreCompleto,
                'timestamp' => $detalle->cancelacion->timestamp_cancelacion
            ],
            'registro' => $detalle->usuario_registro,
            'estatus_viaje' => $detalle->viaje_neto->Estatus,
            'numduplicado' =>$num,
            'code'=>$code
        ];

        return $output;
    }

}
