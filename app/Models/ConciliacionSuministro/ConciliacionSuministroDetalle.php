<?php

namespace App\Models\ConciliacionSuministro;

use App\Reportes\InicioViajes;
use Illuminate\Database\Eloquent\Model;

class ConciliacionSuministroDetalle extends Model
{
    protected $connection = 'sca';
    protected $table = 'conciliacion_suministro_detalle';
    protected $primaryKey = 'idconciliacion_detalle';

    public $timestamps = false;

    protected $fillable = [
        'idconciliacion',
        'idinicioviaje',
        'idviaje',
        'timestamp',
        'estado',
        'registro'
    ];

    public function viaje() {
        return $this->belongsTo(InicioViajes::class, 'idviaje');
    }

    public function viaje_neto() {
        return $this->belongsTo(InicioCamion::class,'idinicioviaje');
    }

    public function conciliacion() {
        return $this->belongsTo(ConciliacionSuministro::class, 'idconciliacion');
    }

    public function cancelacion() {
        return $this->hasOne(ConciliacionSuministroDetalleCancelacion::class, 'idconciliaciondetalle');
    }

    public function save(array $options = array()) {
        if($this->conciliacion->estado != 0 && $this->estado != -1 ){
            throw new \Exception("No se pueden relacionar más viajes a la conciliación.");
        }else{

            //$v = ViajeNeto::find($this->idviaje_neto);
            //dd("llega aqui");
//            $preexistente = $v->conciliacionDetalles->where('estado', 1)->first();
//            if(!$preexistente){
            $this->removerNoConciliados();
            //}
            parent::save($options);
        }
    }

    private function removerNoConciliados(){
        $v = InicioCamion::find($this->idviaje_neto);
        $no_concilados_coincidentes = $this->conciliacion->ConciliacionSuministroDetallesNoConciliados
            ->where('Code',$v->Code);
        foreach($no_concilados_coincidentes as $ncc){
            $ncc->delete();
        }
    }

    public function getUsuarioRegistroAttribute(){
        $usuario = User::find($this->registro);
        if($usuario){
            return $usuario->present()->nombreCompleto;
        }
        return "";
    }
}
