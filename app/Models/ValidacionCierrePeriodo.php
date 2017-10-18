<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ValidacionCierrePeriodo extends Model
{
    //

    use \Laracasts\Presenter\PresentableTrait;

    protected $connection = 'sca';
    protected $table = 'validacion_x_cierre_periodo';
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

    public static function usuario_cierres($id){

        $cierres = DB::connection('sca')->select(DB::raw("SELECT * FROM validacion_x_cierre_periodo vcp
                                                            inner join cierres_periodo cp ON vcp.idcierre_periodo = cp.idcierre
                                                            where vcp.idusuario = {$id} and now() between fecha_inicio and fecha_fin;"));

        return $cierres;

    }
    public static function permiso_usuario($id, $mes, $anio){

        $cierres = DB::connection('sca')->select(DB::raw("SELECT * FROM validacion_x_cierre_periodo vcp
                                                            inner join cierres_periodo cp ON vcp.idcierre_periodo = cp.idcierre
                                                            where vcp.idusuario = {$id} and now() between fecha_inicio and fecha_fin and  cp.mes=$mes and cp.anio=$anio;"));

        if($cierres==[]){
            return NULL;
        }else {
            return $cierres;
        }

    }

    public static function cierreUsuario($id, $mes, $anio){

        $cierres = DB::connection('sca')->select(DB::raw("SELECT count(*) as permiso  FROM validacion_x_cierre_periodo vcp
                                                            inner join cierres_periodo cp ON vcp.idcierre_periodo = cp.idcierre
                                                            where vcp.idusuario = {$id} and now() between fecha_inicio and fecha_fin and  cp.mes=$mes and cp.anio=$anio"));
        return $cierres[0]->permiso;
    }
    public static function DatosPermisosUsuarios(){
        $historial = DB::connection('sca')->select(DB::raw("SELECT distinct(vcp.id), vcp.fecha_inicio, vcp.fecha_fin, cp.idcierre, cp.mes, cp.anio,u.nombre FROM validacion_x_cierre_periodo vcp
                                                            inner join cierres_periodo cp ON vcp.idcierre_periodo = cp.idcierre
                                                            left join sca_configuracion.vw_usuarios u ON u.id_usuario = vcp.idusuario
                                                            order by vcp.fecha_inicio"));
        foreach ($historial as $h){
            $extra [] = [
                'idcierre' => $h->idcierre,
                'mes' => CierrePeriodo::nombreMeses($h->mes),
                'anio' => $h->anio,
                'nombre' => $h->nombre,
                'fecha_inicio'=>$h->fecha_inicio,
                'fecha_fin' =>$h->fecha_fin
            ];
        }
        return $extra;
    }
}
