<?php

namespace App\Models\ConflictosSuministros;

use App\Models\InicioCamion;
use Illuminate\Database\Eloquent\Model;

class InicioSuministroPagable extends Model
{
    protected $connection = 'sca';
    protected $table = 'inicio_camion_conflictos_pagables';
    protected $primaryKey = 'id';
    protected $fillable = [
        'idinicio_camion',
        'idconflicto',
        'aprobo_pago',
        'motivo',

    ];
    public $timestamps = false;

    public function viaje(){
        return $this->belongsTo(InicioCamion::class, "id");
    }
    public function conflicto(){
        return $this->belongsTo(ConflictoSuministro::class, "idconflicto");
    }
    public function usuario_aprobo_pago(){
        return $this->belongsTo(\App\User::class,"aprobo_pago");
    }
}
