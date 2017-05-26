<?php

namespace App\Models\Camiones;

use Illuminate\Database\Eloquent\Model;

class SolicitudActualizacionImagenes extends Model
{
    protected $connection = 'sca';
    protected $table = 'solicitud_actualizacion_camion_imagenes';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    public function getTipoCStringAttribute(){
        switch ($this->TipoC){
            case 'i':
                return 'Izquierda';
                break;
            case 'f':
                return 'Frente';
                break;
            case 't':
                return 'Atras';
                break;
            case 'd':
                return 'Derecha';
                break;
        }
    }
}
