<?php

namespace App\Models\ConflictosSuministros;

use Illuminate\Database\Eloquent\Model;

class ConflictoSuministro extends Model
{
    protected $connection = 'sca';
    protected $table = 'conflictos_suministro_entre_viajes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function detalles(){
        return $this->hasMany(ConflictoSuministroDetalle::class, "idconflicto");
    }
}
