<?php

namespace App\Models;

use App\Models\ConciliacionSuministro\ConciliacionSuministroDetalle;
use App\Models\ConflictosSuministros\ConflictoSuministro;
use App\Models\ConflictosSuministros\InicioSuministroPagable;
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
        'idcamion',
        'idorigen',
        'fecha_origen',
        'idusuario',
        'idperfil',
        'estatus',
        'idmaterial',
        'folioMina',
        'folioSeguimiento',
        'volumen',
        'Code',
        'numImpresion',
        'tipo',
        'FechaCarga',
        'Version',
        'Aprobo',
        'FechaHoraAprobacion',
        'Rechazo',
        'FechaHoraRechazo',
        'idMotivo',
        'Modifico'
    ];
    protected $presenter = ModelPresenter::class;
    public $timestamps = false;

    public function camion() {
        return $this->belongsTo(Camion::class, 'idcamion');
    }

    public function origen() {
        return $this->belongsTo(Origen::class, 'idorigen');
    }
    public function material() {
        return $this->belongsTo(Material::class, 'idmaterial');
    }
    public function conciliacionDetalles() {
        return $this->hasMany(ConciliacionSuministroDetalle::class, 'idinicioviaje','id');
    }
    public function conflicto_pagable() {
        return $this->hasOne(InicioSuministroPagable::class, 'idinicio_camion');
    }
    public function conflicto_entre_viajes(){
        return $this->hasMany(ConflictoSuministro::class, "idinicio_camion", "id");
    }
    public function viaje() {
        return $this->hasOne(InicioViaje::class, 'IdInicioCamion');
    }

    public function viaje_rechazado() {
        return $this->hasOne(InicioViajesRechazados::class, 'IdInicio');
    }
    public static function scopeReporte($query)
    {

       return $query
            ->leftJoin('igh.usuario as user_autorizo', 'inicio_camion.Aprobo', '=', 'user_autorizo.idusuario')
            ->leftJoin('camiones', 'inicio_camion.idcamion', '=', 'camiones.IdCamion')
            ->leftJoin('materiales', 'inicio_camion.idmaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('origenes', 'inicio_camion.idorigen', '=', 'origenes.IdOrigen')
            ->leftJoin('igh.usuario as user_registro','inicio_camion.idusuario','=','user_registro.idusuario')
            ->leftJoin('igh.usuario as user_primer_toque', 'inicio_camion.idusuario', '=', 'user_primer_toque.idusuario')
            ->leftJoin('conflictos_suministro_entre_viajes_detalle_ultimo as conflicto', 'inicio_camion.id', '=', 'conflicto.idinicio_viaje')
            ->leftJoin('inicio_camion_conflictos_pagables as conflictos_pagables', 'inicio_camion.id', '=', 'conflictos_pagables.idinicio_camion')
            ->leftJoin('inicio_viajes as v', 'inicio_camion.id', '=', 'v.IdInicioCamion')
            ->leftJoin('igh.usuario as user_valido', 'v.Creo', '=', 'user_valido.idusuario')
            ->leftJoin('igh.usuario as user_aprobo_pago', 'conflictos_pagables.aprobo_pago', '=', 'user_aprobo_pago.idusuario')
            ->leftJoin('conciliacion_suministro_detalle as cd', DB::raw("inicio_camion.id = cd.idinicioviaje AND cd.estado"), '=', DB::raw("1"))
            ->leftJoin('conciliacion_suministro as c', 'cd.idconciliacion', '=', 'c.idconciliacion')
            ->leftJoin('igh.usuario as user_concilio', 'c.IdRegistro', '=', 'user_concilio.idusuario')
            ->addSelect(
                "inicio_camion.id as id",
                DB::raw("IF(inicio_camion.Aprobo is not null, CONCAT(user_autorizo.nombre, ' ', user_autorizo.apaterno, ' ', user_autorizo.amaterno), '') as autorizo"),
                "camiones.Economico as camion", "inicio_camion.CubicacionCamion as cubicacion",
                DB::raw("(CASE inicio_camion.Estatus when 1 then 'PENDIENTE DE VALIDAR'
                    when 0 then  'NO VALIDADO (DENEGADO)'
                    when 20 then 'PENDIENTE DE VALIDAR'
                    when 21 then 'VALIDADO'
                    when 22 then 'NO AUTORIZADO (RECHAZADO)'
                    when 29 then 'CARGADO'
                    END) as estado"),
                "materiales.Descripcion as material",
                "origenes.Descripcion as origen",
                DB::raw("IF(user_registro.idusuario is not null, CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno), inicio_camion.idusuario) as registro"),
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as registro_primer_toque"),
                DB::raw("IF(inicio_camion.tipo = 1 OR inicio_camion.tipo = null, 'APLICACIÓN MOVIL', 'APLICACIÓN MOVIL') as tipo"),
                DB::raw("CONCAT(user_valido.nombre, ' ', user_valido.apaterno, ' ', user_valido.amaterno) as valido"),
                "conflicto.idconflicto as conflicto",
                DB::raw("
                IF(conflicto.idconflicto is not null, 
                IF(conflictos_pagables.id is not null, 
                CONCAT('EN CONFLICTO PUESTO PAGABLE POR ', user_aprobo_pago.nombre, ' ' , user_aprobo_pago.apaterno, ' ', user_aprobo_pago.amaterno, ':', conflictos_pagables.motivo),
                'EN CONFLICTO (NO PAGABLE)'),
                 'SIN CONFLICTO') as conflicto_pdf"),
                "conflictos_pagables.id as conflicto_pagable",
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
            ->where('inicio_camion.estatus', 20);
    }

    public static function scopeManualesRechazados($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->where('inicio_camion.estatus', 22);
    }
    /**
     * @param $query
     * @return mixed
     */
   /* public static function scopeManualesDenegados($query) {
        return $query->select(DB::raw('inicio_camion.*'))->leftJoin('inicioviajesrechazados', 'inicio_camion.id', '=', 'inicioviajesrechazados.IdInicio')
            ->where(function ($query) {
                $query->whereNotNull('inicioviajesrechazados.IdInicioViajeRechazado')
                    ->where('inicio_camion.Estatus', 21);
            });
    }*/

    public static function scopeMovilesValidados($query) {
        return $query->select(DB::raw('inicio_camion.*'))->leftJoin('inicio_viajes', 'inicio_camion.id', '=', 'inicio_viajes.IdInicioCamion')
            ->where(function($query){
                $query->whereNotNull('inicio_viajes.IdInicioViajes')
                    ->where('inicio_camion.estatus', 21);
            });
    }

    public static function scopeMovilesAutorizados($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->where('inicio_camion.estatus', 1);
    }

    public static function scopeMovilesDenegados($query) {
        return $query->select(DB::raw('inicio_camion.*'))->leftJoin('inicioviajesrechazados', 'inicio_camion.id', '=', 'inicioviajesrechazados.IdInicio')
            ->where(function ($query) {
                $query->whereNotNull('inicioviajesrechazados.IdInicioViajeRechazado')
                    ->where('inicio_camion.estatus', 0);
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
        return $query->select(DB::raw('inicio_camion.*, camiones.idcamion as idcamion, camiones.economico as camion,  camiones.CubicacionParaPago AS cubicacion, origenes.descripcion as origen, origenes.idorigen AS idorigen, materiales.descripcion as material, materiales.idmaterial as idmaterial'))
            ->leftJoin('inicio_viajes', 'inicio_camion.id', '=', 'inicio_viajes.IdInicioCamion')
            ->leftJoin('inicioviajesrechazados', 'inicio_camion.id', '=', 'inicioviajesrechazados.IdInicio')
            ->leftJoin('camiones', 'inicio_camion.idcamion', '=', 'camiones.idcamion')
            ->leftJoin('origenes', 'origenes.idorigen', '=', 'inicio_camion.idorigen')
            ->leftJoin('materiales', 'materiales.idmaterial', '=', 'inicio_camion.idmaterial')
            ->where(function($query){
                $query
                    ->whereNull('inicio_viajes.IdInicioViajes')
                    ->whereNull('inicioviajesrechazados.IdInicioViajeRechazado')
                    ->whereIn('inicio_camion.Estatus', [0, 10, 20, 30])
                    ->where('tipo','=','1');
            });
    }
    public function scopeValidados($query) {
        return $query->select(DB::raw('inicio_camion.*'))
            ->leftJoin('inicio_viajes', 'inicio_camion.id', '=', 'inicio_viajes.IdInicioCamion')
            ->where(function($query){
                $query
                    ->whereNotNull('inicio_viajes.IdInicioViaje')
                    ->whereIn('inicio_camion.estatus', [11, 21, 31]);
            });
    }
    public function valido() {
           /* $fecha = $this->fecha_origen;
            dd($fecha);*/

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
       }elseif ($this->folioSeguimiento == null){
           return 'El suministro no puede ser registrado porque debe ingresar su folio de seguimiento';
       }
       else {
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
        try {
            DB::connection('sca')->beginTransaction();
            if($data["Accion"] == 1) { //validar
                $inicio_camion = InicioCamion::find($this->id);
                if($data["Cubicacion"] < $data["Volumen"]){
                    DB::connection('sca')->rollback();
                    $msg = "EL volumen es mayor a la cubicacion del suministro.";
                    return ['message' => $msg];
                }

                DB::connection('sca')->table('inicio_viajes')->insert([
                    'IdInicioCamion'        => $this->id,
                    'FechaCarga' =>Carbon::now()->toDateTimeString(),
                    'IdProyecto'    => 1,
                    'IdCamion'      => $this->idcamion,
                    'CubicacionCamion'  => $data["Cubicacion"],
                    'IdOrigen'        => $this->idorigen ,
                    'Fecha' => $this->fecha_origen,
                    'IdMaterial'      => $this->idmaterial,
                    'IdChecador'    => auth()->user()->idusuario,
                    'creo'        => $this->idusuario,
                    'Estatus'    => 0,
                    'code'      => $this->code,
                    'uidTAG'=> $this->uidTAG,
                    'folioMina'        =>$data["FolioMina"],
                    'folioSeguimiento' => $data["FolioSeguimiento"],
                    'Volumen'    => $data["Volumen"],
                    'numImpresion'      => $this->numImpresion,
                    'Registro'           => auth()->user()->idusuario
                ]);

                $inicio_camion->estatus = 21; // Validado el viaje
                $inicio_camion->Modifico = auth()->user()->idusuario;
                /*$inicio_camion->folioMina = $data["FolioMina"];
                $inicio_camion->folioSeguimiento = $data["FolioSeguimiento"];
                $inicio_camion->volumen = $data["Volumen"];*/

                $inicio_camion->save();

                $msg = "Se valido el suministro";
            }else if ($data["Accion"]== 0){ //Rechazar
                $inicio_camion = InicioCamion::find($this->id);
                if($data["FolioMina"]=="") {
                    $data["FolioMina"] = null;
                }
                if($data["FolioSeguimiento"]=="") {
                    $data["FolioSeguimiento"] = null;
                }
                if($data["Volumen"]==""){
                    $data["Volumen"]='0.00';
                }
                 DB::connection('sca')->table('inicioviajesrechazados')->insert([
                     'IdInicio'        => $this->id,
                     'FechaRechazo' =>Carbon::now()->toDateTimeString(),
                     'IdProyecto'    => 1,
                     'IdCamion'      => $this->idcamion,
                     'CubicacionCamion'  =>  $data["Cubicacion"],
                     'IdOrigen'        => $this->idorigen ,
                     'Fecha' => $this->fecha_origen,
                     'IdMaterial'      => $this->idmaterial,
                     'Rechazo'           => auth()->user()->idusuario,
                     'Creo'        => $this->idusuario,
                     'Estatus'    => 0,
                     'folioMina'        =>$data["FolioMina"],
                     'folioSeguimiento' => $data["FolioSeguimiento"],
                     'Volumen'    => $data["Volumen"],
                 ]);
                $inicio_camion->estatus=0;
                $inicio_camion->Rechazo = auth()->user()->idusuario;
                $inicio_camion->FechaHoraRechazo = Carbon::now()->toDateTimeString();
                $inicio_camion->save();
                $msg = "Se Rechazo el suministro correctamente.";
            }
            DB::connection('sca')->commit();
            return ['message' => $msg, 'tipo'=>"success"];
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
                DB::connection('sca')->table('cambio_suministro_material')->insert([
                    'IdInicioCamion'        => $this->id ,
                    'IdMaterialAnterior' => $this->idmaterial,
                    'IdMaterialNuevo'    => $data['IdMaterial'],
                    'FechaRegistro'      => Carbon::now()->toDateTimeString(),
                    'Registro'           => auth()->user()->idusuario
                ]);
                $this->IdMaterial = $data['IdMaterial'];
            }

            if($this->IdOrigen != $data['IdOrigen']) {
                DB::connection('sca')->table('cambio_suministro_origen')->insert([
                    'IdInicioCamion'      => $this->id ,
                    'IdOrigenAnterior' => $this->idorigen,
                    'IdOrigenNuevo'    => $data['IdOrigen'],
                    'FechaRegistro'    => Carbon::now()->toDateTimeString(),
                    'Registro'         => auth()->user()->idusuario
                ]);
                $this->IdOrigen = $data['IdOrigen'];
            }

            if($this->CubicacionCamion != $data['Cubicacion']) {
                DB::connection('sca')->table('cambio_suministro_volumen')->insert([
                    'IdInicioCamion'   => $this->id,
                    'FechaRegistro' => Carbon::now()->toDateTimeString(),
                    'VolumenViejo'  => $this->CubicacionCamion,
                    'VolumenNuevo'  => $data['Cubicacion']
                ]);
                $this->CubicacionCamion = $data['Cubicacion'];
            }
            if($this->folioMina != $data['FolioMina'] || $this->folioSeguimiento != $data['FolioSeguimiento']) {
                DB::connection('sca')->table('cambio_folios_suministro')->insert([
                    'IdInicioCamion'   => $this->id,
                    'FechaRegistro' => Carbon::now()->toDateTimeString(),
                    'FolioMinaAnterior'  => $this->folioMina,
                    'FolioMinaNuevo'  => $data['FolioMina'],
                    'FolioSegAnterior'  => $this->folioSeguimiento,
                    'FolioSegNuevo'  => $data['FolioSeguimiento']
                ]);
                $this->folioSeguimiento = $data['FolioSeguimiento'];
                $this->folioMina = $data['FolioMina'];
            }
            $this->Modifico = auth()->user()->idusuario;
            $this->save();

            DB::connection('sca')->commit();

            return ['message' => 'Viaje Modificado Correctamente',
                'tipo' => 'success',
                'viaje' => [
                    'CubicacionCamion' => $this->CubicacionCamion,
                    'IdOrigen' => $this->idorigen,
                    'Origen' => $this->origen->Descripcion,
                    'Material' => $this->material->Descripcion,
                    'IdMaterial' => $this->idmaterial,
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

    public static function scopeEnConflicto($query) {
        return $query->select(DB::raw('inicio_camion.*, cd.id as idconflictodetalle, cd.idconflicto as conflicto, cd.idinicio_viaje'))
            ->join('conflictos_suministro_entre_viajes_detalle_ultimo as cd', 'inicio_camion.id', '=', 'cd.idinicio_viaje');
    }

    public function getConflictoAttribute(){
        #Máximo Id de conflicto
        $resultado = DB::connection('sca')->select(DB::raw('select max(idconflicto) as idconflicto from conflictos_suministro_entre_viajes_detalle
        where idinicio_viaje = '.$this->id));
        $idconflicto = $resultado[0]->idconflicto;
        $conflicto = ConflictosSuministros\ConflictoSuministro::find($idconflicto);
        return $conflicto;
    }
    public function getEnConflictoTiempoAttribute(){

        if($this->conflicto){
            if($this->conflicto->estado == 1){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }
    public function getDescripcionConflictoAttribute(){
        $codigos =  "";
        if($this->en_conflicto_tiempo){
            if((count($this->conflicto->detalles)-1) == 1){
                $codigos =  "Este viaje entra en conflicto con otro viaje pues el tiempo entre ellos es menor a 30 minutos.";
            }else{
                $codigos =  "Este viaje entra en conflicto con ".(count($this->conflicto->detalles)-1)." viajes pues el tiempo entre ello es menor a 30 minutos.";
            }

            $detalles = $this->conflicto->detalles;
            foreach($detalles as $detalle){
                //if($detalle->viaje_neto->IdViajeNeto != $this->IdViajeNeto){
                //dd(16,strlen($detalle->viaje_neto->Code));
                $codigos.= "\n".$detalle->viaje_neto->code.str_repeat("\t",(20-strlen($detalle->viaje_neto->code))) . "\t[Llegada: ".$detalle->viaje_neto->fecha_origen."]";
                // }
            }
            $codigos .= "\n Los viajes en conflicto deben ser presentados a aclaración para su cobro.";
            return $codigos;
        }else{
            return "";
        }
    }

    public function getDescripcionConflictoAlertAttribute(){
        //dd($this->conflicto);
        $codigos =  "";
        if($this->en_conflicto_tiempo){
            if((count($this->conflicto->detalles)-1) == 1){
                $codigos =  "Este viaje entra en conflicto con otro viaje pues el tiempo entre ellos es menor a 30 minutos.";
            }else{
                $codigos =  "Este viaje entra en conflicto con ".(count($this->conflicto->detalles)-1)." viajes pues el tiempo entre ello es menor a 30 minutos.";
            }
            $codigos.="<br/><br/><table class='table table-striped' style='font-size:0.8em'><thead>"
                . "<tr><th style='text-align:center'>Código</th><th style='text-align:center'>Salida</th><th style='text-align:center'>Llegada</th></tr></thead>";
            $detalles = $this->conflicto->detalles;
            foreach($detalles as $detalle){
                //if($detalle->viaje_neto->IdViajeNeto != $this->IdViajeNeto){
                $codigos.= "<tr><td>".$detalle->viaje_neto->code. "</td><td>".$detalle->viaje_neto->fecha_origen."</td></tr>";
                // }
            }
            $codigos.="</table>";
            $codigos.= "<br>Los viajes en conflicto deben ser presentados a aclaración para su cobro.";
            return $codigos;
        }else{
            return "";
        }
    }

    public function poner_pagable($request){
        if(str_replace(" ", "", $request->get("motivo"))==""){
            throw new \Exception("Indique el motivo para permitir el pago del viaje en conflicto");
        }

        InicioSuministroPagable::create([
            "idinicio_camion"=>$this->id,
            "idconflicto"=>$request->IdConflicto,
            "motivo"=>$request->motivo,
            "aprobo_pago"=>auth()->user()->idusuario
        ]);
    }

    public function getEstadoAttribute() {
        switch ($this->estatus) {
            case 1 :
                return "PENDIENTE DE VALIDAR";
                break;
            case 0:
                return "NO VALIDADO (DENEGADO)";
                break;
            case 20:
                return "PENDIENTE DE VALIDAR";
                break;
            case 21:
                return "VALIDADO";
                break;
            case 22:
                return "NO AUTORIZADO (RECHAZADO)";
                break;
            case 29 :
                return "CARGADO";
                break;
            default: return "";
        }
    }

}
