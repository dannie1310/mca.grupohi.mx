<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;
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
        return $query->select(DB::raw('inicio_camion.*, camiones.idcamion as idcamion, camiones.economico as camion, camiones.CubicacionParaPago as cubicacion, origenes.descripcion as origen, origenes.idorigen AS idorigen, materiales.descripcion as material, materiales.idmaterial as idmaterial'))
            ->leftJoin('inicio_viajes', 'inicio_camion.id', '=', 'inicio_viajes.IdInicioCamion')
            ->leftJoin('inicioviajesrechazados', 'inicio_camion.id', '=', 'inicioviajesrechazados.IdInicio')
            ->leftJoin('camiones', 'inicio_camion.idcamion', '=', 'camiones.idcamion')
            ->leftJoin('origenes', 'origenes.idorigen', '=', 'inicio_camion.idorigen')
            ->leftJoin('materiales', 'materiales.idmaterial', '=', 'inicio_camion.idmaterial')
            ->where(function($query){
                $query
                    ->whereNull('inicio_viajes.IdInicioViajes')
                    ->whereNull('inicioviajesrechazados.IdInicioViajeRechazado')
                    ->whereIn('inicio_camion.Estatus', [0, 10, 20, 30]);//cambiar estatus
            });
    }

    public function valido() {

            if($this->idmaterial == 0 || $this->Estatus == 10 ) {
                return false;
            } else {
                return true;
            }
    }

    public function estado() {
       if($this->Estatus == 10) {
            return 'El suministro no puede ser registrado porque debe seleccionar primero su origen';
       } elseif ($this->folioMina == null ) {
           return 'El suministro no puede ser registrado porque debe ingresar su folio de mina';
       }
       elseif ($this->folioSeguimiento == null){
           return 'El suministro no puede ser registrado porque debe ingresar su folio de seguimiento';
       } else {
            return 'El viaje es valido para su registro';
       }
    }

    public static function validandoCierre($FechaLlegada){
        /* Bloqueo de cierre de periodo
            1 : Cierre de periodo
            0 : Periodo abierto.
        */

        $fecha = Carbon::createFromFormat('Y-m-d H:m:i', $FechaLlegada);
        $cierres = DB::connection('sca')->select(DB::raw("SELECT COUNT(*) as existe FROM cierres_periodo where mes = '{$fecha->month}' and anio = '{$fecha->year}'"));
        $validarUss=ValidacionCierrePeriodo::permiso_usuario(Auth::user()->idusuario,$fecha->month,$fecha->year);

        if($cierres[0]->existe != 0) {
            if ($validarUss == NULL) {
                $datos = 1;
            }else {
                $datos = 0;
            }
        }else{
            $datos = 0;
        }

        return $datos;
    }

    public function validar($request) { //editar para validar los viajes

        $data = $request->get('data');

        DB::connection('sca')->beginTransaction();
        try {
            $statement ="call sca_sp_registra_viaje_fda_v2("
                .$data["Accion"].","
                .$this->IdViajeNeto.","
                ."0".","
                ."0".","
                .$this->origen->IdOrigen.","
                .($this->IdSindicato ? $this->IdSindicato : 'NULL').","
                .($data["IdSindicato"] ? $data['IdSindicato'] : 'NULL').","
                .($this->IdEmpresa ? $this->IdEmpresa : 'NULL').","
                .($data["IdEmpresa"] ? $data['IdEmpresa'] : 'NULL').","
                .auth()->user()->idusuario.",'"
                .$data["TipoTarifa"]."','"
                .$data["TipoFDA"]."',"
                .$data["Tara"].","
                .$data["Bruto"].","
                .$data["Cubicacion"].","
                .$this->CubicacionCamion. ","
                .($this->deductiva ? $this->deductiva->id : 'NULL'). ","
                .($this->deductiva ? $this->deductiva->estatus : 'NULL') .
                ",@a, @v);";

            DB::connection("sca")->statement($statement);

            $result = DB::connection('sca')->select('SELECT @a,@v');
            if($result[0]->{'@a'} == 'ok') {
                $msg = $data['Accion'] == 1 ? 'Viaje validado exitosamente' : 'Viaje Rechazado exitosamente';
                $tipo = $data['Accion'] == 1 ? 'success' : 'info';
            } else {
                $msg = 'Error: ' . $result[0]->{'@v'};
                $tipo = 'error';
            }

            DB::connection('sca')->commit();
            return ['message' => $msg,
                'tipo' => $tipo];
        } catch (Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }

    public function modificar($request) {//modificar
        $data = $request->get('data');
        $viaje_aprobado = $this->viaje;
        if($viaje_aprobado)
            throw new \Exception("El este viaje suministrado no puede ser modificado porque ya se encuentra validado.");

        DB::connection('sca')->beginTransaction();
        try {

            if($this->IdMaterial != $data['IdMaterial']) {//crear tablas
                DB::connection('sca')->table('cambio_material')->insert([
                    'IdViajeNeto'        => $this->IdViajeNeto ,
                    'IdMaterialAnterior' => $this->IdMaterial,
                    'IdMaterialNuevo'    => $data['IdMaterial'],
                    'FechaRegistro'      => Carbon::now()->toDateTimeString(),
                    'Registro'           => auth()->user()->idusuario
                ]);
                $this->IdMaterial = $data['IdMaterial'];
            }

            if($this->IdOrigen != $data['IdOrigen']) {
                DB::connection('sca')->table('cambio_origen')->insert([
                    'IdViajeNeto'      => $this->IdViajeNeto ,
                    'IdOrigenAnterior' => $this->IdOrigen,
                    'IdOrigenNuevo'    => $data['IdOrigen'],
                    'FechaRegistro'    => Carbon::now()->toDateTimeString(),
                    'Registro'         => auth()->user()->idusuario
                ]);
                $this->IdOrigen = $data['IdOrigen'];
            }

            if($this->CubicacionCamion != $data['CubicacionCamion']) {
                DB::connection('sca')->table('cambio_cubicacion')->insert([
                    'IdViajeNeto'   => $this->IdViajeNeto,
                    'FechaRegistro' => Carbon::now()->toDateTimeString(),
                    'VolumenViejo'  => $this->CubicacionCamion,
                    'VolumenNuevo'  => $data['CubicacionCamion']
                ]);
                $this->CubicacionCamion = $data['CubicacionCamion'];
            }
            $this->Modifico = auth()->user()->idusuario;
            $this->save();

            DB::connection('sca')->commit();

            return ['message' => 'Viaje Modificado Correctamente',
                'tipo' => 'success',
                'viaje' => [
                    'CubicacionCamion' => $this->CubicacionCamion,
                    'IdOrigen' => $this->IdOrigen,
                    'Origen' => $this->origen->Descripcion,
                    'Material' => $this->material->Descripcion,
                    'IdMaterial' => $this->IdMaterial,
                    'folioMina' => (String) $this->folioMina,
                    'folioSeguimiento' => (String) $this->folioSeguimiento,
                    'volumen' => $this->Volumen
                ]
            ];
        } catch (Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }

}
