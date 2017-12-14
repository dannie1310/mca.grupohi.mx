<?php

namespace App\Models\Transformers;

use Themsaid\Transformers\AbstractTransformer;
use Illuminate\Database\Eloquent\Model;

class InicioCamionTransformer extends AbstractTransformer
{
    public function transformModel(Model $viaje_neto) {

        return [
            'id'                => $viaje_neto->id,
            'autorizo'          => $viaje_neto->autorizo,
            'camion'            => (String) $viaje_neto->camion->Economico,
            'codigo'            => $viaje_neto->code,
            'cubicacion'        => $viaje_neto->camion->CubicacionParaPago,
            'estado'            => $viaje_neto->getEstadoAttribute(),
            'estatus'           => $viaje_neto->Estatus,
            'idmaterial'       => $viaje_neto->idmaterial,
            'idorigen'         => $viaje_neto->idorigen,
            'material'          => (String) $viaje_neto->material->Descripcion,
            'origen'            => (String) $viaje_neto->origen->Descripcion,
            'registro'          => $viaje_neto->idusuario,
            'timestamp_llegada' => $viaje_neto->fecha_origen,
            'fecha_hora_carga' => $viaje_neto->FechaCarga,
            'tipo'              => "APLICACIÓN MÓVIL",
            'valido'            => $viaje_neto->registro,
            'motivo'            =>$viaje_neto->motivo,
            'conflicto'         => $viaje_neto->conflicto->id,
            'conflicto_pdf'     => ($viaje_neto->conflicto->id)?($viaje_neto->conflicto_pagable)?'EN CONFLICTO PUESTO PAGABLE POR '.$viaje_neto->conflicto_pagable->usuario_aprobo_pago->present()->NombreCompleto.':'.$viaje_neto->conflicto_pagable->motivo:'EN CONFLICTO (NO PAGABLE)':'SIN CONFLICTO',
            'conflicto_pagable' => ($viaje_neto->conflicto_pagable)?$viaje_neto->conflicto_pagable->id:'',
        ];
    }
}