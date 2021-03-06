<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Presenters\ModelPresenter;
use Jenssegers\Date\Date;

class Origen extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    
    protected $connection = 'sca';
    protected $table = 'origenes';
    protected $primaryKey = 'IdOrigen';
    protected $fillable = ['IdTipoOrigen', 'IdProyecto', 'Descripcion', 'FechaAlta', 'HoraAlta','usuario_registro','motivo','usuario_desactivo','Estatus','interno','usuario_edito'];
    protected $presenter = ModelPresenter::class;

    public function getCreatedAtAttribute($timestamp) {
        return new Date($timestamp);
    }

    public function proyectoLocal() {
        return $this->belongsTo(ProyectoLocal::class, 'IdProyecto');
    }
    
    public function tipoOrigen() {
        return $this->belongsTo(TipoOrigen::class, 'IdTipoOrigen');
    }
    
    public function rutas() {
        return $this->hasMany(Ruta::class, 'IdOrigen');
    }
    
    public function __toString() {
        return $this->Descripcion;
    }
    public function user_registro(){
        return $this->belongsTo(\App\User::class, 'usuario_registro','idusuario');
    }
    public function user_edito(){
        return $this->belongsTo(User::class, 'usuario_edito', 'idusuario');
    }
    public function usuario() {
        return $this->belongsToMany(\App\User::class, \App\Facades\Context::getDatabaseName().'.origen_x_usuario', 'idorigen', 'idusuario_intranet');
    }

    public function fdaBancoMaterial() {
        return $this->hasMany(FDA\FDABancoMaterial::class, 'IdBanco', 'IdOrigen');
    }
    
    public function viajesNetos() {
        return $this->hasMany(ViajeNeto::class, 'IdOrigen');
    }
    
    public function tiros() {
        return $this->belongsToMany(Tiro::class, 'rutas', 'IdOrigen', 'IdTiro')
            ->orderBy('tiros.Descripcion', 'ASC');
    }
}