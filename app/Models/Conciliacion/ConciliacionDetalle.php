<?php

namespace App\Models\Conciliacion;

use App\Models\Material;
use App\Models\Viaje;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ConciliacionDetalle extends Model
{
    protected $connection = 'sca';
    protected $table = 'conciliacion_detalle';
    protected $primaryKey = 'idconciliacion_detalle';

    public $timestamps = false;

    protected $fillable = [
        'idconciliacion',
        'idviaje_neto',
        'idviaje',
        'timestamp',
        'estado'
    ];

    public function viaje() {
        return $this->belongsTo(Viaje::class, 'idviaje');
    }

    public function conciliacion() {
        return $this->belongsTo(Conciliacion::class, 'idconciliacion');
    }

    public function cancelacion() {
        return $this->hasOne(ConciliacionDetalleCancelacion::class, 'idconciliaciondetalle');
    }
    
    public function save(array $options = array()) {
        if($this->conciliacion->estado != 0 && $this->estado != -1 ){
            throw new \Exception("No se pueden relacionar más viajes a la conciliación.");
        }else{
            parent::save($options);
        }
    }


//    public function save(){
//        throw new Exception("La conciliación se encuentra cerrada");
//    }

   
}
