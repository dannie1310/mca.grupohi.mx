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
            ->leftJoin('empresas as empresas_viajes', 'v.IdEmpresa', '=', 'empresas_viajes.IdEmpresa')
            ->leftJoin('empresas as empresas_camiones', 'camiones.IdEmpresa', '=', 'empresas_camiones.IdEmpresa')
            ->leftJoin('sindicatos as sindicatos_viajes', 'v.IdSindicato', '=', 'sindicatos_viajes.IdSindicato')
            ->leftJoin('conciliacion_suministro_detalle as cd', DB::raw("inicio_camion.id = cd.idinicioviaje AND cd.estado"), '=', DB::raw("1"))
            ->leftJoin('conciliacion_suministro as c', 'cd.idconciliacion', '=', 'c.idconciliacion')
            ->leftJoin('igh.usuario as user_concilio', 'c.IdRegistro', '=', 'user_concilio.idusuario')
            ->addSelect(
                "inicio_camion.id as id",
                DB::raw("IF(inicio_camion.Aprobo is not null, CONCAT(user_autorizo.nombre, ' ', user_autorizo.apaterno, ' ', user_autorizo.amaterno), '') as autorizo"),
                "camiones.Economico as camion",
             //   DB::raw("IF(inicio_camion.CubicacionCamion <= 8, camiones.CubicacionParaPago, viajesnetos.CubicacionCamion) as cubicacion"),
              //  "viajesnetos.CubicacionCamion as cubicacion",
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
               /* DB::raw("IF(v.Importe is not null, v.Importe,
                IF(viajesnetos.CubicacionCamion <= 8,
                ((tarifas.PrimerKM*1*camiones.CubicacionParaPago)+(tarifas.KMSubsecuente*rutas.KmSubsecuentes*camiones.CubicacionParaPago)+(tarifas.KMAdicional*rutas.KmAdicionales*camiones.CubicacionParaPago))
                ,
                ((tarifas.PrimerKM*1*viajesnetos.CubicacionCamion)+(tarifas.KMSubsecuente*rutas.KmSubsecuentes*viajesnetos.CubicacionCamion)+(tarifas.KMAdicional*rutas.KmAdicionales*viajesnetos.CubicacionCamion))
                )) as importe"),*/
             /*   DB::raw("CONCAT(user_valido.nombre, ' ', user_valido.apaterno, ' ', user_valido.amaterno) as valido"),
                "conflicto.idconflicto as conflicto",
                DB::raw("
                IF(conflicto.idconflicto is not null,
                IF(conflictos_pagables.id is not null,
                CONCAT('EN CONFLICTO PUESTO PAGABLE POR ', user_aprobo_pago.nombre, ' ' , user_aprobo_pago.apaterno, ' ', user_aprobo_pago.amaterno, ':', conflictos_pagables.motivo),
                'EN CONFLICTO (NO PAGABLE)'),
                 'SIN CONFLICTO') as conflicto_pdf"),
                "conflictos_pagables.id as conflicto_pagable",
                "empresas_viajes.razonSocial as empresa_viaje",
                "empresas_viajesnetos.razonSocial as empresa_viajeneto",
                "empresas_camiones.razonSocial as empresa_camion",
                "sindicatos_viajes.NombreCorto as sindicato_viaje",
                "sindicatos_viajesnetos.NombreCorto as sindicato_viajeneto",
                "sindicatos_camiones.NombreCorto as sindicato_camion",*/
                DB::raw("group_concat(c.idconciliacion) as id_conciliacion"),
                DB::raw("group_concat(CONCAT(user_concilio.nombre, ' ', user_concilio.apaterno, ' ', user_concilio.amaterno)) as concilio"),
                DB::raw("group_concat(c.fecha_conciliacion) as fecha_conciliacion"),
                DB::raw("CONCAT(inicio_camion.FechaCarga, ' ', inicio_camion.HoraCarga) as fecha_hora_carga")
            )
            ->groupBy('inicio_camion.id');


    }
}
