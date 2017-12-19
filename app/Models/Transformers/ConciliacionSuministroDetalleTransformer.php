<?php

namespace App\Models\Transformers;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Themsaid\Transformers\AbstractTransformer;

class ConciliacionSuministroDetalleTransformer extends AbstractTransformer
{

    public function transformModel(Model $detalle) {

        $output = [
            'idconciliacion_detalle' => $detalle->idconciliacion_detalle,
            'id'                     => ($detalle->viaje_neto->id),
            'timestamp_llegada'      => $detalle->viaje_neto->fecha_origen,
            'cubicacion_camion'      => $detalle->viaje_neto->volumen,
            'camion'                 => $detalle->viaje_neto->camion->Economico,
            'material'               => $detalle->viaje_neto->material->Descripcion,
            'code'                   => $detalle->viaje_neto->code,
            'folioMina'              => $detalle->viaje_neto->folioMina,
            'folioSeg'               => $detalle->viaje_neto->folioSeguimiento,
            'estado'                 => $detalle->estado,
            'cancelacion'            => $detalle->estado == 1 ? [] : [
                'motivo' => $detalle->cancelacion->motivo,
                'cancelo' => User::find($detalle->cancelacion->idcancelo)->present()->nombreCompleto,
                'timestamp' => $detalle->cancelacion->timestamp_cancelacion
            ],
            'registro' => $detalle->usuario_registro,
            'estatus_viaje' => $detalle->viaje_neto->Estatus
        ];

        return $output;
    }
}
