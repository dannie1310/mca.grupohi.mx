<?php

namespace App\Models\ConflictosSuministros;

use App\Models\InicioCamion;
use Illuminate\Database\Eloquent\Model;

class ConflictoSuministroDetalle extends Model
{
    protected $connection = 'sca';
    protected $table = 'conflictos_suministro_entre_viajes_detalle';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function conflicto_entre_viajes(){
        return $this->belongsTo(ConflictoSuministro::class, "idconflicto");
    }
    public function viaje_neto(){
        return $this->belongsTo(InicioCamion::class, "idinicio_viaje");
    }
}
