<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InicioCamion extends Model
{
    use \Laracasts\Presenter\PresentableTrait;

    protected $connection = 'sca';
    protected $table = 'inicio_camion';
    protected $primaryKey = 'id';
    protected $fillable = [
        'IdCamion',
        'IdOrigen',
        'fecha_origen',
        'idusuario',
        'idperfil',
        'estatus',
        'IdMaterial',
        'folioMina',
        'folioSeguimiento',
        'volumen',
        'Code',
        'numImpresion',
        'tipo'
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
        return $this->hasMany(ConciliacionSuministroDetalle::class, 'idinicioviaje','id');
    }

}
