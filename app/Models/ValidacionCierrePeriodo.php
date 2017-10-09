<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ValidacionCierrePeriodo extends Model
{
    //

    use \Laracasts\Presenter\PresentableTrait;

    protected $connection = 'sca';
    protected $table = 'validacion_x_cierre_periodo;';
    protected $primaryKey = 'id';
    protected $fillable = [
        'idusuario',
        'fecha_inicio',
        'fecha_fin',
        'usuario_registro',
        'idcierre_periodo'
    ];
    public $timestamps = false;
    protected $dates = ['fecha_registro'];

    public function cierresPeriodo() {
        return $this->hasMany(CierrePeriodo::class, "idcierre", "idcierre_periodo");
    }

    public static function usuario_cierres($id, $mes, $anio){

        $cierres = DB::connection('sca')->select(DB::raw("SELECT * FROM validacion_x_cierre_periodo vcp
                                                            inner join cierres_periodo cp ON vcp.idcierre_periodo = cp.idcierre
                                                            where vcp.idusuario = {$id} and now() between fecha_inicio and fecha_fin and  cp.mes=$mes and cp.anio=$anio;"));

        return $cierres;

    }

}
