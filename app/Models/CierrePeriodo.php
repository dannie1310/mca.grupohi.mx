<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Auth;

class CierrePeriodo extends Model
{
    //
    protected $connection = 'sca';
    protected $table = 'cierres_periodo';
    protected $primaryKey = 'idcierre';
    protected $fillable = ['mes', 'anio', 'usuario', 'registro'];
    protected $presenter = ModelPresenter::class;

    public $timestamps = false;

    public static function cierres()
    {
        $cierres = CierrePeriodo::orderBy('idcierre')->get();

        foreach ($cierres as $cierre) {

            $extra [] = [
                'idcierre' => $cierre->idcierre,
                'mes' => $cierre->mes,
                'mesNombre' => self::nombreMeses($cierre->mes),
                'anio' => $cierre->anio
            ];
        }

        return $extra;
    }

    public static function nombreMeses($mes)
    {

        switch ($mes) {
            case 1:
                $nombre = 'Enero';
                break;
            case 2:
                $nombre = 'Febrero';
                break;
            case 3:
                $nombre = 'Marzo';
                break;
            case 4:
                $nombre = 'Abril';
                break;
            case 5:
                $nombre = 'Mayo';
                break;
            case 6:
                $nombre = 'Junio';
                break;
            case 7:
                $nombre = 'Julio';
                break;
            case 8:
                $nombre = 'Agosto';
                break;
            case 9:
                $nombre = 'Septiembre';
                break;
            case 10:
                $nombre = 'Octubre';
                break;
            case 11:
                $nombre = 'Noviembre';
                break;
            case 12:
                $nombre = 'Diciembre';
                break;
        }
        return $nombre;
    }

    public static function cierresPeriodos(){
        /* Bloqueo de cierre de periodo
           1 : Cierre de periodo
           0 : Periodo abierto.
       */

        $cierres = DB::connection('sca')->select(DB::raw("SELECT * FROM cierres_periodo"));
        $extra=[];

        foreach ($cierres as $c) {
            $a = ValidacionCierrePeriodo::cierreUsuario(Auth::user()->idusuario, $c->mes, $c->anio);

            if ($a == 0) {
                $extra [] = [
                    'idcierre' => $c->idcierre,
                    'mes' => $c->mes,
                    'anio' => $c->anio
                ];
            }
        }
        return $extra;
    }
}
