<?php

namespace App\Models\Transformers;

use Illuminate\Database\Eloquent\Model;
use App\Models\InicioViaje;
use App\User;
use Themsaid\Transformers\AbstractTransformer;

class ConciliacionSuministroDetalleNoConciliadoTransformer extends AbstractTransformer
{

    public function transformModel(Model $detalle_nc) {

        $output = [
            'id' => $detalle_nc->id,
            'idmotivo' => $detalle_nc->idmotivo,
            'idinicioviaje' => $detalle_nc->idinicioviaje,
            'idviaje' => $detalle_nc->idviaje,
            'Code' => $detalle_nc->Code,
            'detalle' =>($detalle_nc->detalle),
            'detalle_alert' =>($detalle_nc->detalle_alert),
            'timestamp' => $detalle_nc->timestamp->format("d-m-Y h:i:s"),
            'registro' => $detalle_nc->usuario_registro,
        ];
        return $output;
    }
}
