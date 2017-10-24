<?php

namespace App\Models\Transformers;

use App\Models\ValidacionCierrePeriodo;
use App\Models\Viaje;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class ViajeTransformerRevertir extends AbstractTransformer
{

    public function transformModel(Model $viaje) {
        $output = [
            'IdViaje' => $viaje->IdViaje,
            'FechaLlegada' => $viaje->FechaLlegada,
            'Tiro' => $viaje->tiro->Descripcion,
            'Camion' => $viaje->camion->Economico,
            'HoraLlegada' => $viaje->HoraLlegada,
            'Cubicacion' => $viaje->CubicacionCamion,
            'Origen' => $viaje->origen->Descripcion,
            'Material' => $viaje->material->Descripcion,
            'Estatus' => $viaje->Estatus,
            'Codigo' => $viaje->code,
            'CierrePeriodo' => ValidacionCierrePeriodo::cierreUsuarioFecha($viaje->FechaLlegada)
        ];

        return $output;
    }
}