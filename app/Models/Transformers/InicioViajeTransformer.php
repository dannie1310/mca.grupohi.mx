<?php

namespace App\Models\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class InicioViajeTransformer extends AbstractTransformer
{

    public function transformModel(Model $viaje) {
        dd("aqui");
        $output = [
            'id'                => $viaje->IdInicioViaje,
            'timestamp_llegada' => $viaje->FechaSalida.' ('.$viaje->HoraSalida.')',
            'cubicacion_camion' => $viaje->CubicacionCamion,
            'camion'            => $viaje->camion->Economico,
            'material'          => $viaje->material->Descripcion,
            'importe'           => number_format($viaje->Importe, 2, '.', ','),
            'code'              => $viaje->code,
            'estatus'           => 0
        ];
        return $output;
    }

}
