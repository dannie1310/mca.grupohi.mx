<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class InicioCamion extends Model
{
    use \Laracasts\Presenter\PresentableTrait;

    protected $connection = 'sca';
    protected $table = 'inicio_camion';
    protected $primaryKey = 'id';
    protected $fillable = [
        'IdCamion',
        'IdOrigen',
        'fecha_origen',
        'idusuario',
        'idperfil',
        'estatus',
        'IdMaterial',
        'folioMina',
        'folioSeguimiento',
        'volumen',
        'Code',
        'numImpresion',
        'tipo'
    ];
    protected $presenter = ModelPresenter::class;
    public $timestamps = false;

    public function camion() {
        return $this->belongsTo(Camion::class, 'IdCamion');
    }

    public function origen() {
        return $this->belongsTo(Origen::class, 'IdOrigen');
    }
    public function material() {
        return $this->belongsTo(Material::class, 'IdMaterial');
    }
    public function conciliacionDetalles() {
        return $this->hasMany(ConciliacionSuministroDetalle::class, 'idinicioviaje','id');
    }
    public static function scopeReporte($query)
    {

       return $query
            ->leftJoin('igh.usuario as user_autorizo', 'inicio_camion.Aprobo', '=', 'user_autorizo.idusuario')
            ->leftJoin('camiones', 'inicio_camion.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('materiales', 'inicio_camion.IdMAterial', '=', 'materiales.IdMAterial')
            ->leftJoin('origenes', 'inicio_camion.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('igh.usuario as user_primer_toque', 'inicio_camion.idusuario', '=', 'user_primer_toque.idusuario')
          //  ->leftJoin('conflictos_entre_viajes_detalle_ultimo as conflicto', 'viajesnetos.IdViajeNeto', '=', 'conflicto.idviaje_neto')
           // ->leftJoin('viajes_netos_conflictos_pagables as conflictos_pagables', 'viajesnetos.IdViajeNeto', '=', 'conflictos_pagables.idviaje_neto')
            ->leftJoin('inicio_viajes as v', 'inicio_camion.id', '=', 'v.IdInicioCamion')
            ->leftJoin('igh.usuario as user_valido', 'v.Creo', '=', 'user_valido.idusuario')
            //->leftJoin('igh.usuario as user_aprobo_pago', 'conflictos_pagables.aprobo_pago', '=', 'user_aprobo_pago.idusuario')
            ->leftJoin('conciliacion_suministro_detalle as cd', DB::raw("inicio_camion.id = cd.idinicioviaje AND cd.estado"), '=', DB::raw("1"))
            ->leftJoin('conciliacion_suministro as c', 'cd.idconciliacion', '=', 'c.idconciliacion')
            ->leftJoin('igh.usuario as user_concilio', 'c.IdRegistro', '=', 'user_concilio.idusuario')
            ->addSelect(
                "inicio_camion.id as id",
                DB::raw("IF(inicio_camion.Aprobo is not null, CONCAT(user_autorizo.nombre, ' ', user_autorizo.apaterno, ' ', user_autorizo.amaterno), '') as autorizo"),
                "camiones.Economico as camion", "inicio_camion.CubicacionCamion as cubicacion",
                DB::raw("(CASE inicio_camion.Estatus when 0 then 'PENDIENTE DE VALIDAR'
                    when 1 then (IF(v.IdInicioViajes is null, 'NO VALIDADO (DENEGADO)', 'VALIDADO'))
                    when 20 then 'PENDIENTE DE VALIDAR'
                    when 21 then (IF(v.IdInicioViajes is null, 'NO VALIDADO (DENEGADO)', 'VALIDADO'))
                    when 22 then 'NO AUTORIZADO (RECHAZADO)'
                    when 29 then 'CARGADO'
                    END) as estado"),
                "materiales.Descripcion as material",
                "origenes.Descripcion as origen",
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as registro_primer_toque"),
                DB::raw("IF(inicio_camion.Estatus = 0 OR inicio_camion.Estatus = 1, 'APLICACIÓN MOVIL', 'MANUAL') as tipo"),
                DB::raw("group_concat(c.idconciliacion) as id_conciliacion"),
                DB::raw("group_concat(CONCAT(user_concilio.nombre, ' ', user_concilio.apaterno, ' ', user_concilio.amaterno)) as concilio"),
                DB::raw("group_concat(c.fecha_conciliacion) as fecha_conciliacion"),
                DB::raw("inicio_camion.FechaCarga as fecha_hora_carga")
            )
            ->groupBy('inicio_camion.id');


    }

    /**
     * @param $query
     * @return mixed
     */
    public static function scopeRegistradosManualmente($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->where('inicio_camion.Estatus', 29);
    }
    public static function scopeFechas($query,Array $fechas) {
        return $query->whereBetween('inicio_camion.fecha_origen', [$fechas['FechaInicial'], $fechas['FechaFinal']]);
    }
    public static function scopeConciliados($query, $conciliados) {
        if($conciliados == 'C') {
            return $query
                ->where(function($query){
                    $query->whereNotNull('cd.idinicioviaje')
                        ->orWhere('cd.estado', '!=', '-1');
                });
        } else if($conciliados == 'NC') {
            return $query
                ->where(function($query){
                    $query->whereNull('cd.idinicioviaje')
                        ->orWhere('cd.estado', '=', '-1');
                });
        } else if($conciliados == 'T') {
            return $query;
        }
    }
    public static function scopeManualesAutorizados($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->where('inicio_camion.Estatus', 20);
    }

    public static function scopeManualesRechazados($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->where('viajesnetos.Estatus', 22);
    }
    /**
     * @param $query
     * @return mixed
     */
    public static function scopeManualesDenegados($query) {
        return $query->select(DB::raw('inicio_camion.*'))->leftJoin('inicioviajesrechazados', 'inicio_camion.id', '=', 'inicioviajesrechazados.IdInicio')
            ->where(function ($query) {
                $query->whereNotNull('inicioviajesrechazados.IdInicioViajeRechazado')
                    ->where('inicio_camion.Estatus', 21);
            });
    }

    public static function scopeMovilesValidados($query) {
        return $query->select(DB::raw('inicio_camion.*'))->leftJoin('inicio_viajes', 'inicio_camion.id', '=', 'inicio_viajes.IdInicioCamion')
            ->where(function($query){
                $query->whereNotNull('inicio_viajes.IdInicioViajes')
                    ->where('inicio_camion.estatus', 1);
            });
    }

    public static function scopeMovilesAutorizados($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->where('inicio_camion.estatus', 0);
    }

    public static function scopeMovilesDenegados($query) {
        return $query->select(DB::raw('inicio_camion.*'))->leftJoin('inicioviajesrechazados', 'inicio_camion.id', '=', 'inicioviajesrechazados.IdInicio')
            ->where(function ($query) {
                $query->whereNotNull('inicioviajesrechazados.IdInicioViajeRechazado')
                    ->where('inicio_camion.estatus', 1);
            });
    }
    /**
     * @param $query
     * @return mixed
     */
    public static function scopeManualesValidados($query) {
        return $query->select(DB::raw('inicio_camion.*'))->leftJoin('inicio_viajes', 'inicio_camion.id', '=', 'inicio_viajes.IdInicioCamion')
            ->where(function($query){
                $query->whereNotNull('inicio_viajes.IdInicioViaje')
                    ->where('inicio_camion.estatus', 21);
            });
    }

    public function scopePorValidar($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->leftJoin('inicio_viajes', 'inicio_camion.id', '=', 'inicio_viajes.IdInicioCamion')
            ->leftJoin('inicioviajesrechazados', 'inicio_camion.id', '=', 'inicioviajesrechazados.IdInicio')
            ->where(function($query){
                $query
                    ->whereNull('inicio_viajes.IdInicioViajes')
                    ->whereNull('inicioviajesrechazados.IdInicioViajeRechazado')
                    ->whereIn('inicio_camion.Estatus', [0, 10, 20, 30]);
            });
    }

}
