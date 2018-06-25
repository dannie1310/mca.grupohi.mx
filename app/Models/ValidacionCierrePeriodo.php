<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;

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
        $historial = DB::connection('sca')->select(DB::raw("SELECT distinct(vcp.id), vcp.fecha_inicio, vcp.fecha_fin, cp.idcierre, cp.mes, cp.anio, 
                                                            CONCAT(u.nombre, ' ', u.apaterno, ' ',u.amaterno) AS nombre, 
                                                            IF(now() < vcp.fecha_fin,1,0) AS estatus FROM validacion_x_cierre_periodo vcp
                                                            inner join cierres_periodo cp ON vcp.idcierre_periodo = cp.idcierre
                                                            left join igh.usuario u ON u.idusuario = vcp.idusuario
                                                            order by vcp.fecha_inicio DESC "));

        $extra = array();
        foreach ($historial as $h) {
            $extra [] = [
                'idcierre' => $h->idcierre,
                'mes' => CierrePeriodo::nombreMeses($h->mes),
                'anio' => $h->anio,
                'nombre' => $h->nombre,
                'fecha_inicio' => $h->fecha_inicio,
                'fecha_fin' => $h->fecha_fin,
                'estatus' => $h->estatus
            ];
        }
        return $extra;
    }
    public static function cierreUsuarioFecha($fecha){
        $f = Carbon::createFromFormat('Y-m-d', $fecha);
        $cierrescerrados= CierrePeriodo::cierrePeriodo($f->month,$f->year);

        return $cierrescerrados;
    }

    public static function validandoCierreViaje($FechaLlegada){
        /* Bloqueo de cierre de periodo
             1 : Cierre de periodo
             0 : Periodo abierto.
         */
        $fecha = Carbon::createFromFormat('Y-m-d', $FechaLlegada);
        $cierres = DB::connection('sca')->select(DB::raw("SELECT COUNT(*) as existe FROM cierres_periodo where mes = '{$fecha->month}' and anio = '{$fecha->year}'"));
        $validarUss=ValidacionCierrePeriodo::permiso_usuario(Auth::user()->idusuario,$fecha->month,$fecha->year);
        if($cierres[0]->existe == 1) {//cierre periodo
            if ($validarUss == NULL) {
                //cierre periodo bloqueado para el usuario
                $datos = 1;
            }else {
                //cierre periodo abierto para el usuario
                $datos = 0;
            }
        }else{//periodo abierto
            $datos = 0;
        }
        return $datos;
    }

    public static function validandoCierreViajeDenegar($FechaLlegada){
        /* Bloqueo de cierre de periodo
             1 : Cierre de periodo
             0 : Periodo abierto.
         */
        $fecha = Carbon::createFromFormat('Y-m-d', $FechaLlegada);
        $cierres = DB::connection('sca')->select(DB::raw("SELECT COUNT(*) as existe FROM cierres_periodo where mes = '{$fecha->month}' and anio = '{$fecha->year}'"));
        $validarUss=ValidacionCierrePeriodo::permiso_usuario(Auth::user()->idusuario,$fecha->month,$fecha->year);
        if($cierres[0]->existe == 1) {//cierre periodo
            $datos = 1;
        }else{//periodo abierto
            $datos = 0;
        }
        return $datos;
    }
}
