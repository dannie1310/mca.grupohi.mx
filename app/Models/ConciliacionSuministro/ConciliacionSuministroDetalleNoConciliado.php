<?php

namespace App\Models\ConciliacionSuministro;

use App\Models\InicioCamion;
use App\Models\InicioViaje;
use Illuminate\Database\Eloquent\Model;

class ConciliacionSuministroDetalleNoConciliado extends Model
{
    protected $connection = 'sca';
    protected $table = 'conciliacion_suministro_detalle_no_conciliado';

    public $timestamps = false;

    protected $fillable = [
        'idconciliacion',
        'idmotivo',
        'idinicioviaje',
        'idviaje',
        'Code',
        'detalle',
        'detalle_alert',
        'timestamp',
        'estado',
        'registro'
    ];

    protected $dates = ["timestamp"];

    public function viaje() {
        return $this->belongsTo(InicioViaje::class, 'idviaje');
    }
    public function viaje_neto() {
        return $this->belongsTo(InicioCamion::class,'id', 'idinicioviaje');
    }
    public function conciliacion() {
        return $this->belongsTo(ConciliacionSuministro::class, 'idconciliacion');
    }

    public function save(array $options = array()) {
        if($this->conciliacion->estado != 0 && $this->estado != -1 ){
            throw new \Exception("No se pueden relacionar más viajes fallidos a la conciliación.");
        }else{
            $preexistente = $this->conciliacion->ConciliacionSuministroDetallesNoConciliados->where('idmotivo',$this->idmotivo)
                ->where('Code', $this->Code)
                ->where('idinicioviaje', $this->idinicioviaje)
                ->where('detalle', $this->detalle)
                ->first();
            if(!$preexistente){
                parent::save($options);
            }
        }
    }
    public function registro(){
        return $this->belongsTo(User::class, "registro");
    }
    public function getUsuarioRegistroAttribute(){
        $usuario = User::find($this->registro);
        if($usuario){
            return $usuario->present()->nombreCompleto;
        }
        return "";
    }
}
