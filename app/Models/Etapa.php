<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Presenters\ModelPresenter;
use App\User;
class Etapa extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    
    protected $connection = 'sca';
    protected $table = 'etapasproyectos';
    protected $primaryKey = 'IdEtapaProyecto';
    protected $fillable = [
        'IdProyecto', 
        'Nivel', 
        'Descripcion'
    ];
    protected $presenter = ModelPresenter::class;
    public $timestamps = false;

    public function proyectoLocal() {
        return $this->belongsTo(ProyectoLocal::class, 'IdProyecto');
    }
    public function user_registro() {
        return $this->belongsTo(User::class, 'usuario_registro', 'idusuario');
    }
}