<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    // Datos de inicio de configuracion
    protected $connection = 'sca';
    protected $table = 'tags';
    protected $primaryKey = 'uid';

    //Informacion de campos llenables
    protected $fillable = ["idcamion","idproyecto_global","fecha_asignacion", "estado", "asigno"];

    // Validacion de registro de TimeStamps
    public $timestamps = false;

}