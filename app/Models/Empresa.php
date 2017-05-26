<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Presenters\ModelPresenter;
use App\User;
class Empresa extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    
    protected $connection = 'sca';
    protected $table = 'empresas';
    protected $primaryKey = 'IdEmpresa';
    protected $fillable = [
        'razonSocial',
        'RFC',
        'usuario_registro',
        'usuario_desactivo',
        'motivo'
    ];

    protected $presenter = ModelPresenter::class;

    public function camiones() {
        return $this->hasMany(Camion::class, 'IdEmpresa');
    }

    /**
     * @return mixed
     */
    public function __toString() {
        return $this->razonSocial;
    }

    public function conciliaciones(){
        return $this->hasMany(Conciliacion::class, "idempresa", "IdEmpresa");
    }
    public function user_registro() {
        return $this->belongsTo(User::class, 'usuario_registro', 'idusuario');
    }
}
