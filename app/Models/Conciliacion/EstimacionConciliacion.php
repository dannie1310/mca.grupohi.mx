<?php

namespace App\Models\Conciliacion;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EstimacionConciliacion extends Model
{
    protected $connection = 'sca';
    protected $table = 'estimacion_conciliacion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_conciliacion',
        'id_estimacion',
    ];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model) {
            $model->registro = auth()->user()->usuario;
            $model->FechaHoraRegistro = Carbon::now()->toDateTimeString();
        });
    }
}
