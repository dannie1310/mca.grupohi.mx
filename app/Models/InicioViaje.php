<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'FechaSalida',
        'HoraSalida',
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
        return $this->hasMany(ConciliacionSuministroDetalle::class, 'idviaje','IdInicioViaje');
    }

}
