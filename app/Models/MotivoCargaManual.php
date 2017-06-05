<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotivoCargaManual extends Model
{
    protected $connection = 'sca';
    protected $table = 'motivos_carga_manual';
    protected $fillable = [
        "descripcion"
    ];
}
