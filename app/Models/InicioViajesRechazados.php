<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InicioViajesRechazados extends Model
{
    protected $connection = 'sca';
    protected $table = 'inicioviajesrechazados';
    protected $primaryKey = 'IdInicioViajeRechazado';
    public $timestamps = false;

    public function inicioCamion() {
        return $this->belongsTo(InicioCamion::class, 'IdInicio');
    }
    public function getUsuarioRegistroAttribute(){
        $usuario = User::find($this->Creo);
        if($usuario){
            return $usuario->present()->nombreCompleto;
        }
        return "";
    }
    public function getTimestampRegistroAttribute(){
        $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaRechazo);
        return $timestamp;
    }
}
