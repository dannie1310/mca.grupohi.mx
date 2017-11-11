<?php

namespace App\Models\ConciliacionSuministro;

use Illuminate\Database\Eloquent\Model;

class ConciliacionSuministroCancelacion extends Model
{
    protected $connection = 'sca';
    protected $table = 'conciliacion_suministro_cancelacion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'idconciliacion',
        'motivo',
        'fecha_hora_cancelacion',
        'idcancelo'
    ];

    protected $dates = ['fecha_hora_cancelacion'];
    public $timestamps = false;


    public function user() {
        return $this->belongsTo(User::class, 'idcancelo');
    }

    public function conciliacion() {
        return $this->belongsTo(ConciliacionSuministro::class, 'idconciliacion');
    }

    public function getTimestampCancelacionAttribute(){
        return ucwords($this->fecha_hora_cancelacion->formatLocalized('%d %B %Y')).' ('.$this->fecha_hora_cancelacion->format("h:i:s").')';
    }
}
