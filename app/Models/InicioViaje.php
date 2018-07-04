<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ConciliacionSuministro\ConciliacionSuministroDetalle;
use App\Models\InicioCamion;

class InicioViaje extends Model
{
    use \Laracasts\Presenter\PresentableTrait;

    protected $connection = 'sca';
    protected $table = 'inicio_viajes';
    protected $primaryKey = 'IdInicioViajes';
    protected $fillable = [
        'IdInicioCamion',
        'IdSindicato',
        'IdEmpresa',
        'FechaCarga',
        'HoraCarga',
        'IdProyecto',
        'IdCamion',
        'CubicacionCamion',
        'IdOrigen',
        'fecha_origen',
        'Creo',
        'uidtag',
        'estatus',
        'IdMaterial',
        'folioMina',
        'folioSeguimiento',
        'volumen',
        'Code',
        'numImpresion',
        'tipo',
        'Elimino',
        'Modifico'
    ];
    protected $presenter = ModelPresenter::class;
    public $timestamps = false;

    public function camion() {
        return $this->belongsTo(Camion::class, 'IdCamion');
    }

    public function origen() {
        return $this->belongsTo(Origen::class, 'IdOrigen');
    }
    public function material() {
        return $this->belongsTo(Material::class, 'IdMaterial');
    }
    public function conciliacionDetalles() {
        return $this->hasMany(ConciliacionSuministroDetalle::class, 'idviaje','IdInicioViajes');
    }
    public function inicio_camion() {
        return $this->belongsTo(InicioCamion::class, 'IdInicioCamion');
    }
    public function scopePorConciliar($query) {
        return $query->leftJoin('conciliacion_suministro_detalle', 'inicio_viajes.IdInicioViajes', '=', 'conciliacion_suministro_detalle.idviaje')
            ->where(function($query){
                $query->whereNull('conciliacion_suministro_detalle.idviaje')
                    ->orWhere('conciliacion_suministro_detalle.estado', '=', '-1');
            });
    }

    public function scopeConciliados($query) {
        return $query->leftJoin('conciliacion_suministro_detalle', 'inicio_viajes.IdInicioViajes', '=', 'conciliacion_suministro_detalle.idviaje')
            ->where(function($query){
                $query->whereNotNull('conciliacion_suministro_detalle.idviaje')
                    ->orWhere('conciliacion_suministro_detalle.estado', '!=', '-1');
            });
    }
    public function disponible() {

        foreach ($this->conciliacionDetalles as $conciliacionDetalle) {
            if ($conciliacionDetalle->estado == 1) {
                return false;
            }
        }
        return true;
    }


}
