<?php

namespace App\Models\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class InicioViajeTransformer extends AbstractTransformer
{

    public function transformModel(Model $viaje) {
        $output = [
            'id'                => $viaje->IdInicioViajes,
            'timestamp_llegada' => $viaje->Fecha,
            'cubicacion_camion' => $viaje->volumen,
            'folioMina'         => $viaje->folioMina,
            'folioSeg'          => $viaje->folioSeguimiento,
            'camion'            => $viaje->camion->Economico,
            'material'          => $viaje->material->Descripcion,
            'code'              => $viaje->code,
            'estatus'           => 0
        ];
        return $output;
    }

}
