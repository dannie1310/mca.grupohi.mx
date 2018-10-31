<?php

namespace App\Models\Tarifas;

use App\Models\TipoTarifa;
use App\User;
use Illuminate\Database\Eloquent\Model;

class TarifaRutaMaterial extends Model
{
    use \Laracasts\Presenter\PresentableTrait;

    protected $connection = 'sca';
    protected $table = 'tarifas_ruta_material';
    protected $primaryKey = 'id';
    protected $fillable = [ 'idtipo_tarifa',
        'id_ruta',
        'id_material',
        'primer_km',
        'km_subsecuentes',
        'km_adicionales',
        'estatus',
        'registra',
        'desactivo',
        'motivo_desactivar',
        'cancelo',
        'motivo_cancelar'
    ];
    protected $dates = ["fecha_hora_registro","inicio_vigencia","fin_vigencia"];
    protected $presenter = ModelPresenter::class;
    public function ruta() {
        return $this->belongsTo(\App\Models\Ruta::class, 'id_ruta');
    }
    public function material() {
        return $this->belongsTo(\App\Models\Material::class, 'id_material');
    }
    public function registro()
    {
        return $this->belongsTo(User::class, "registra");
    }

   /* public function getFinVigenciaTarifaAttribute(){
        if($this->FinVigencia){
            return $this->FinVigencia->format("d-m-Y h:i:s");
        }else{
            return "VIGENTE";
        }
    }*/

    public function getEstatusStringAttribute() {
        if($this->estatus == 1){
            return 'ACTIVA';
        }
        if($this->estatus == 2){
            return 'CANCELADA';
        }
        if($this->estatus == 0){
            return 'INACTIVA';
        }
    }

    public function user_desactivo() {
        return $this->belongsTo(User::class, 'desactivo');
    }

    public function user_cancelo() {
        return $this->belongsTo(User::class, 'cancelo');
    }

    public function TipoTarifas() {
        return $this->belongsTo(TipoTarifa::class, 'idtipo_tarifa');
    }
}
