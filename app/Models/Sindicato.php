<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Presenters\ModelPresenter;
use App\Models\Conciliacion\Conciliacion;
use App\User;
class Sindicato extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    
    protected $connection = 'sca';
    protected $table = 'sindicatos';
    protected $primaryKey = 'IdSindicato';
    protected $fillable = ['Descripcion', 'NombreCorto','rfc','usuario_registro','usuario_desactivo','motivo'];

    protected $presenter = ModelPresenter::class;

    public function camiones() {
        return $this->hasMany(Camion::class, 'IdSindicato');
    }

    /**
     * @return mixed
     */
    public function __toString() {
        return $this->Descripcion;
    }
    
    public function conciliaciones(){
        return $this->hasMany(Conciliacion::class, "idsindicato", "IdSindicato");
    }
    public function user_registro() {
        return $this->belongsTo(User::class, 'usuario_registro', 'idusuario');
    }
}