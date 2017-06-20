<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CierrePeriodo extends Model
{
    //
    protected $connection = 'sca';
    protected $table = 'cierres_periodo';
    protected $primaryKey = 'idcierre';
    protected $fillable = ['mes', 'anio', 'usuario', 'registro'];
    protected $presenter = ModelPresenter::class;

    public $timestamps = false;
}
