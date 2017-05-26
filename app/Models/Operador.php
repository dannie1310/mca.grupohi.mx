<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Presenters\ModelPresenter;
use App\User;
class Operador extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    
    protected $connection = 'sca';
    protected $table = 'operadores';
    protected $primaryKey = 'IdOperador';
    protected $fillable = [
        'IdProyecto', 
        'Nombre', 
        'Direccion', 
        'NoLicencia', 
        'VigenciaLicencia', 
        'FechaAlta',
        'usuario_registro',
        'usuario_desactivo',
        'motivo'
    ];
    protected $presenter = ModelPresenter::class;


    public function camiones() {
        return $this->hasMany(Camion::class, 'IdOperador');
    }
    
    public function __toString() {
        return $this->Nombre;
    }

    public function user_registro() {
        return $this->belongsTo(User::class, 'usuario_registro', 'idusuario');
    }

}
