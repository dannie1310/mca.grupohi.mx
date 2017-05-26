<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Presenters\ModelPresenter;
use App\User;
class Marca extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    
    protected $connection = 'sca';
    protected $table = 'marcas';
    protected $primaryKey = 'IdMarca';
    protected $fillable = ['Descripcion'];
    protected $presenter = ModelPresenter::class;
    
    public $timestamps = false;
    
    public function camiones() {
        return $this->hasMany(Camion::class, 'IdMarca');
    }
    
    public function __toString() {
        return $this->Descripcion;
    }
    public function user_registro() {
        return $this->belongsTo(User::class, 'usuario_registro', 'idusuario');
    }
}