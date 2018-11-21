<?php

namespace App\Models;

use App\Models\Cortes\CorteCambio;
use App\Models\Cortes\CorteDetalle;
use App\User;
use Auth;
use Couchbase\Exception;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use App\Presenters\ModelPresenter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Conciliacion\ConciliacionDetalle;
use App\Models\Conflictos\ViajeNetoConflictoPagable;
use App\Models\FolioValeManual;
use Jenssegers\Date\Date;

class ViajeNeto extends Model
{
    use \Laracasts\Presenter\PresentableTrait;

    protected $connection = 'sca';
    protected $table = 'viajesnetos';
    protected $primaryKey = 'IdViajeNeto';
    protected $fillable = [
        'FechaCarga',
        'HoraCarga',
        'IdProyecto',
        'IdCamion',
        'IdOrigen',
        'FechaSalida',
        'HoraSalida',
        'IdTiro',
        'FechaLlegada',
        'HoraLlegada',
        'IdMaterial',
        'Observaciones',
        'Creo',
        'Estatus',
        'Code',
        'CubicacionCamion',
        'IdEmpresa',
        'IdSindicato',
        'IdMotivo',
        'denegado'
    ];
    protected $presenter = ModelPresenter::class;
    public $timestamps = false;
    protected $dates = ['FechaHoraAprobacion', 'FechaHoraRechazo'];

    public function conciliacionDetalles() {
        return $this->hasMany(ConciliacionDetalle::class, "idviaje_neto", "IdViajeNeto");
    }

    public function proyectoLocal() {
        return $this->belongsTo(ProyectoLocal::class, 'IdProyecto');
    }

    public function camion() {
        return $this->belongsTo(Camion::class, 'IdCamion');
    }

    public function origen() {
        return $this->belongsTo(Origen::class, 'IdOrigen');
    }

    public function tiro() {
        return $this->belongsTo(Tiro::class, 'IdTiro');
    }

    public function material() {
        return $this->belongsTo(Material::class, 'IdMaterial');
    }

    public function ruta() {
        return $this->hasOne(Ruta::class, 'IdTiro', 'IdTiro')->where('rutas.IdOrigen', $this->IdOrigen);
    }

    public function tarifaMaterial() {
        return $this->hasManyThrough(Tarifas\TarifaMaterial::class, Material::class, 'IdMaterial', 'IdMaterial', 'IdMaterial');
    }

    public function imagenes() {
        return $this->hasMany(ImagenViajeNeto::class, 'idviaje_neto')->where('estado', 1);
    }

    public function deductiva() {
        return $this->hasOne(Deductiva::class, 'id_viaje_neto', 'IdViajeNeto');
    }

    public static function scopeConciliados($query, $conciliados) {
        if($conciliados == 'C') {
            return $query
                ->where(function($query){
                    $query->whereNotNull('cd.idviaje')
                        ->orWhere('cd.estado', '!=', '-1');
                });
        } else if($conciliados == 'NC') {
            return $query
                ->where(function($query){
                    $query->whereNull('cd.idviaje')
                        ->orWhere('cd.estado', '=', '-1');
                });
        } else if($conciliados == 'T') {
            return $query;
        }
    }


    /**
     * @param $query
     * @return mixed
     */
    public static function scopeRegistradosManualmente($query) {
        return $query->select(DB::raw('viajesnetos.*'))
            ->where('viajesnetos.Estatus', 29);
    }

    public static function scopeFechas($query,Array $fechas) {
        return $query->whereBetween('viajesnetos.FechaLlegada', [$fechas['FechaInicial'], $fechas['FechaFinal']]);
    }
    public static function scopeCodigo($query, $codigo) {
        return $query->where('viajesnetos.Code', $codigo);
    }
    public function scopePorValidar($query) {
        return $query->select(DB::raw('viajesnetos.*'))
            ->leftJoin('viajes', 'viajesnetos.IdViajeNeto', '=', 'viajes.IdViajeNeto')
            ->leftJoin('viajesrechazados', 'viajesnetos.IdViajeNeto', '=', 'viajesrechazados.IdViajeNeto')
            ->where(function($query){
                $query
                    ->whereNull('viajes.IdViaje')
                    ->whereNull('viajesrechazados.IdViajeRechazado')
                    ->whereIn('viajesnetos.Estatus', [0, 10, 20, 30]);
            });
    }

    public function scopeValidados($query) {
        return $query->select(DB::raw('viajesnetos.*'))
            ->leftJoin('viajes', 'viajesnetos.IdViajeNeto', '=', 'viajes.IdViajeNeto')
            ->where(function($query){
                $query
                    ->whereNotNull('viajes.IdViaje')
                    ->whereIn('viajesnetos.Estatus', [1, 11, 21, 31]);
            });
    }

    public static function autorizar($data) {
        $autorizados = 0;
        $error=0;
        DB::connection('sca')->beginTransaction();
        try {
            if(count($data) == 0) {
                $msg = "SELECCIONA AL MENOS UN VIAJE";
            } else {
                foreach ($data as $key => $estatus) {
                    $viaje = ViajeNeto::findOrFail($key);
                    $viaje->Estatus = $estatus;
                    $s = ViajeNeto::validandoCierre($viaje->FechaLlegada);
                    if ($s == 0) {
                        if ($estatus == '22') {
                            $viaje->Rechazo = auth()->user()->idusuario;
                            $viaje->FechaHoraRechazo = Carbon::now()->toDateTimeString();
                            FolioValeManual::where('id_viaje_neto','=',$viaje->IdViajeNeto)->update(['id_viaje_neto' => NULL]);
                        } else {
                            $viaje->Aprobo = auth()->user()->idusuario;
                            $viaje->FechaHoraAprobacion = Carbon::now()->toDateTimeString();
                        }
                        $viaje->save();
                        if ($estatus == "20") {
                            $autorizados += 1;
                        }
                    } else {
                        $error++;
                    }

                }
                if ($error != 0 && $autorizados != 0) {
                    $msg = "VIAJES AUTORIZADOS (" . $autorizados . ")\n VIAJES RECHAZADOS (" . (count($data) - $autorizados) . ") \n VIAJES NO AUTORIZADOS POR PERIODO CERRADO ($error)";
                } elseif ($error == 0 && $autorizados != 0) {
                    $msg = "VIAJES AUTORIZADOS (" . $autorizados . ")\n VIAJES RECHAZADOS (" . (count($data) - $autorizados) . ")";
                } elseif ($error != 0 && $autorizados == 0) {
                        $msg = "VIAJES NO AUTORIZADOS POR PERIODO CERRADO ($error)";
                }else if($error == 0 && $autorizados == 0){
                    $msg = "VIAJES RECHAZADOS (" . (count($data) - $autorizados) . ")";
                }
            }
            DB::connection('sca')->commit();
            return $msg;
        } catch (Exception $ex) {
            DB::connection('sca')->rollback();
        }
    }

    public function rechazar() {
        DB::connection($this->connection)->beginTransaction();
        try {

            $this->Estatus = 22;
            $this->save();

            DB::connection($this->connection)->commit();
        } catch (Exception $ex) {
            DB::connection($this->connection)->rollback();
        }
    }

    public static function cargaManual($request) {
        $error="";
        DB::connection('sca')->beginTransaction();
        $contar=0;

        try {
            foreach($request->get('viajes', []) as $viaje) {

                $viaje_comprobar = ViajeNeto::whereRaw("Code ={$viaje['Codigo']}")
                    ->whereIn('Estatus', [29, 20, 21])->first();

                if ($viaje_comprobar == "") {

                    $ruta = Ruta::where('IdOrigen', $viaje['IdOrigen'])
                        ->where('IdTiro', $viaje['IdTiro'])
                        ->first();
                    $fecha_salida = Carbon::createFromFormat('Y-m-d H:i', $viaje['FechaLlegada'] . ' ' . $viaje['HoraLlegada'])
                        ->subMinutes($ruta->cronometria->TiempoMinimo);

                    $s = ViajeNeto::validandoCierre($viaje['FechaLlegada']);

                    if ($s == 0) {
                        $proyecto_local = ProyectoLocal::where('IdProyectoGlobal', '=', $request->session()->get('id'))->first();
                        $extra = [
                            'FechaCarga' => Carbon::now()->toDateString(),
                            'HoraCarga' => Carbon::now()->toTimeString(),
                            'FechaLlegada' => $viaje['FechaLlegada'],
                            'HoraLlegada' => $viaje['HoraLlegada'],
                            'FechaSalida' => $fecha_salida->toDateString(),
                            'HoraSalida' => $fecha_salida->toTimeString(),
                            'IdProyecto' => $proyecto_local->IdProyecto,
                            'Creo' => auth()->user()->idusuario,
                            'Estatus' => 29,
                            'Code' => $viaje['Codigo'],
                            'IdMotivo' => $viaje['IdMotivo'],
                            'CubicacionCamion' => $viaje['Cubicacion'],
                            'Observaciones' => $viaje['IdMotivo'] == 7 ? $viaje['Motivo'] : MotivoCargaManual::find($viaje['IdMotivo'])->descripcion
                        ];

                        $nuevo_viaje = ViajeNeto::create(array_merge($viaje, $extra));

                        FolioValeManual::where('folio', '=', $viaje['Codigo'])->update(['id_viaje_neto' => $nuevo_viaje->IdViajeNeto]);

                        $contar = $contar + 1;
                    }
                }else{
                    //dd($viaje_comprobar['IdViajeNeto']);
                    $error = "{$viaje_comprobar['Code']}";
                }
            }
            if($contar == count($request->get('viajes'))){

                DB::connection('sca')->commit();
                return [
                    'success' => true,
                    'message' => '¡' . count($request->get('viajes')) . ' VIAJE(S) REGISTRADO(S) CORRECTAMENTE!'
                ];

            }elseif ($error != 0){
                return [
                    'success' => false,
                    'message' => 'Folio(s) ['.$error.'] ya se encuentra.'
                ];
            }
            else{
                return [
                    'success' => false,
                    'message' => 'No puede registrar viajes con periodo cerrados.'
                ];
            }
        } catch (Exception $ex) {
            DB::connection('sca')->rollback();
        }
    }

    public static function cargaManualCompleta($request) {

        DB::connection('sca')->beginTransaction();
        try {
            $registrados = 0;
            $rechazados = 0;
            $rechazadosArray = [];
            foreach($request->get('viajes', []) as $viaje) {
                for($i = 0; $i < $viaje['NumViajes']; $i++) {
                    DB::connection("sca")->statement("call registra_viajes_netos_viajes("
                        .$request->session()->get("id").",'"
                        .$viaje["FechaLlegada"]."',"
                        .$viaje["IdCamion"].","
                        .$viaje["Cubicacion"].","
                        .$viaje["IdOrigen"].","
                        .$viaje["IdTiro"].","
                        .$viaje["IdRuta"].","
                        .$viaje["IdMaterial"].","
                        .$viaje["PrimerKm"].","
                        .$viaje["KmSub"].","
                        .$viaje["KmAd"].",'"
                        .$viaje["Turno"]."','"
                        .$viaje["Observaciones"]."',"
                        .auth()->user()->idusuario
                        .",@OK);"
                    );
                    $result = DB::connection('sca')->select('SELECT @OK');
                    if($result[0]->{'@OK'} == '1') {
                        $registrados += 1;
                    } else {
                        $rechazados += 1;
                        $rechazadosArray [] = $viaje['Id'];
                    }
                }
            }

            $i = 0;
            $str = '';
            foreach($rechazadosArray as $r) {
                if($i == 0) {
                    $str .= $r;
                } else {
                    $str .= ', '.$r;
                }
            }
            DB::connection('sca')->commit();
            return ['message' => "VIAJES AUTORIZADOS (".$registrados.")\n VIAJES NO REGISTRADOS (".$rechazados.")".(count($rechazadosArray) > 0 ? "\n ID's NO REGISTRADOS (".$str.")" : ""),
                'rechazados' => $rechazadosArray];
        } catch (Exception $ex) {
            DB::connection('sca')->rollback();
        }
    }

    public function getTiempo() {
        $timestampSalida = Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaSalida.' '.$this->HoraSalida);
        $timestampLlegada = Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaLlegada.' '.$this->HoraLlegada);

        return $timestampSalida->diffInSeconds($timestampLlegada);
    }

    public function getImporte() {
        if($this->ruta && $this->camion && $this->material) {
            if($this->material->tarifaMaterial){
                return (($this->material->tarifaMaterial->PrimerKM * 1 * $this->CubicacionCamion) +
                    ($this->material->tarifaMaterial->KMSubsecuente * $this->ruta->KmSubsecuentes * $this->CubicacionCamion) +
                    ($this->material->tarifaMaterial->KMAdicional * $this->ruta->KmAdicionales * $this->CubicacionCamion));
            }else{
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function getImporteNuevo() {
        if($this->corte_cambio) {
            if($this->corte_cambio->cubicacion_nueva) {
                if($this->ruta && $this->camion && $this->material) {
                    if($this->material->tarifaMaterial){
                        return (($this->material->tarifaMaterial->PrimerKM * 1 * $this->corte_cambio->cubicacion_nueva) +
                            ($this->material->tarifaMaterial->KMSubsecuente * $this->ruta->KmSubsecuentes * $this->corte_cambio->cubicacion_nueva) +
                            ($this->material->tarifaMaterial->KMAdicional * $this->ruta->KmAdicionales * $this->corte_cambio->cubicacion_nueva));
                    }else{
                        return 0;
                    }
                } else {
                    return 0;
                }
            }
        } else {
            return null;
        }
    }

    public function valido() {
        if(!isset($this->ruta)) {
            return false;
        } else {
            $min = $this->ruta->cronometria->TiempoMinimo;
            $tol = $this->ruta->cronometria->Tolerancia;

            if($this->tarifaMaterial["0"]["Estatus"] == 0){
                return false;
            }else{
                if (!isset($this->material) || count($this->tarifaMaterial) == 0 || $this->Estatus == 10 || ($this->IdPerfil != 3 && $this->Estatus == 0 && ($this->getTiempo() == 0 || (($this->getTiempo() / 60) < ($min - $tol))))) {
                    return false;
                }
                else {
                    return true;
                }
            }
        }
    }

    public function estado() {
        $min = $this->ruta ? $this->ruta->cronometria->TiempoMinimo : null;
        $tol = $this->ruta ? $this->ruta->cronometria->Tolerancia : null;

        if($this->getTiempo() == 0 && $this->Estatus == 0 && $this->IdPerfil!=3) {
            return 'El viaje no puede ser registrado porque el tiempo del viaje es  0.00 min.';
        } else if($this->Estatus == 0 && $this->IdPerfil!=3 && ($this->getTiempo() == 0 || (($this->getTiempo() / 60) < ($min - $tol)))) {
            return 'El viaje no puede ser registrado porque no cumple con los tiempos de cronometría de la ruta';
        } else if(!isset($this->ruta) && $this->Estatus == 0) {
            return 'El viaje no puede ser registrado porque no existe una ruta entre su origen y destino';
        } else if(count($this->tarifaMaterial) == 0 && isset($this->material)) {
            return 'El viaje no puede ser registrado porque no hay una tarifa registrada para su material';
        } else if($this->Estatus == 10) {
            return 'El viaje no puede ser registrado porque debe seleccionar primero su origen';
        } else {
            return 'El viaje es valido para su registro';
        }
    }


    public function validar($request) {

        $data = $request->get('data');
        $error = "";
        $id_tarifa = "";
        $msg = "";
        $tipo = "";
        DB::connection('sca')->beginTransaction();
        try {
            /*
            *    Cubicacion
            */
            if ($data["Cubicacion"] == $this->CubicacionCamion) {
                $cubicacion = $this->CubicacionCamion;
            } else {
                $cubicacion = $data["Cubicacion"];
            }

            /*
             *  Información viajes netos
             */

            $id_viaje_neto = $request->get("IdViajeNeto");
            $viaje_neto = DB::connection('sca')->select(DB::raw("select *, concat(trim(FechaLlegada), ' ', trim(HoraLlegada)) as fecha
                                                            FROM viajesnetos 
                                                            where IdViajeNeto = " . $id_viaje_neto . " "));

            /*
             * Validar existencia de dichos viajes - validado o rechazado.
             */
            $viaje = DB::connection('sca')->select(DB::raw("select * from viajes where IdViajeNeto = " . $id_viaje_neto . " "));
            if ($viaje != []) {
                $error = 'error';
                return ['message' => 'Este viaje ya fue validado con anterioridad.',
                    'tipo' => 'info'];
            }

            $viaje_denegado = DB::connection('sca')->select(DB::raw("select * from viajesrechazados where IdViajeNeto = " . $id_viaje_neto . " "));
            if ($viaje_denegado != []) {
                $error = 'error';
                return ['message' => 'Este viaje ya fue rechazado con anterioridad.',
                    'tipo' => 'info'];
            }

            if ($error == "") {

                /*
                 * Validar viaje
                 */
                if ($data["Accion"] == "1") {
                    /*
          * Información de Rutas
          */
                    $rutas_activas = DB::connection('sca')->select(DB::raw("select count(*) as cantidad, rutas.* 
                                                                    from rutas 
                                                                    where IdOrigen = " . $viaje_neto["0"]->IdOrigen . " and IdTiro = " . $viaje_neto["0"]->IdTiro . " and
                                                                    estatus=1"));

                    if ($rutas_activas["0"]->cantidad == 1) {
                        $ruta_volumen_primerKM = $rutas_activas["0"]->PrimerKm * $cubicacion;
                        $ruta_volumen_kmsubsecuentes = $rutas_activas["0"]->KmSubsecuentes * $cubicacion;
                        $ruta_volumen_kmadicionales = $rutas_activas["0"]->KmAdicionales * $cubicacion;
                    } elseif ($rutas_activas["0"]->cantidad > 1) {
                        $error = "Hay mas de una ruta activa para el origen y destino del viaje, favor de deshabilitar una";
                        $tipo = 'error';
                        return ['message' => $error,
                            'tipo' => $tipo];
                    } elseif ($rutas_activas["0"]->cantidad == 0) {
                        $error = "No cuenta con una ruta activa";
                        $tipo = 'error';
                        return ['message' => $error,
                            'tipo' => $tipo];
                    }

                    if ($ruta_volumen_primerKM != 0 && $ruta_volumen_kmsubsecuentes != 0) {
                        $ruta_volumen = $ruta_volumen_primerKM + $ruta_volumen_kmsubsecuentes + $ruta_volumen_kmadicionales;
                    } else {
                        $error = "Error la tarifa no es valida";
                        $tipo = 'error';
                        return ['message' => $error,
                            'tipo' => $tipo];
                    }

                    /*
                     * Verificar la crnometria del viaje
                     */
                    $min = $this->ruta->cronometria->TiempoMinimo;
                    $tol = $this->ruta->cronometria->Tolerancia;

                    $cronometria = DB::connection('sca')->select(DB::raw("select Idcronometria from cronometrias where IdRuta = " . $rutas_activas["0"]->IdRuta . " limit 1"));

                    if ($this->getTiempo() == 0 && $this->Estatus == 0 && $this->IdPerfil != 3) {
                        $error = "El viaje no puede ser registrado porque el tiempo del viaje es  0.00 min.";
                        $tipo = 'error';
                        return ['message' => $error,
                            'tipo' => $tipo];
                    }
                    if ($this->IdPerfil != 3 && ($this->getTiempo() == 0 || (($this->getTiempo() / 60) < ($min - $tol)))) {
                        $error = "El viaje no puede ser registrado porque no cumple con los tiempos de cronometría de la ruta";
                        $tipo = 'error';
                        return ['message' => $error,
                            'tipo' => $tipo];
                    }
                    if ($cronometria["0"]->Idcronometria == 0) {
                        $error = "La cronometria no es valida";
                        $tipo = 'error';
                        return ['message' => $error,
                            'tipo' => $tipo];
                    }
                    $ruta_volumen = $ruta_volumen_primerKM + $ruta_volumen_kmsubsecuentes + $ruta_volumen_kmadicionales;


                    /*--------------------------------------------------------------------
                     -- ---------------------------- Obtener Fda ---------------------------
                     -- --------------------------------------------------------------------
                     IF v_TipoFdA = 'bm' then
                        SET @Fda = (select FactorAbundamiento from factorabundamiento_material where IdMaterial = @IdMaterial and IdBanco = v_IdOrigen and Estatus = 1 order by TimestampAlta desc limit 1 );
                     else
                       IF v_TipoFdA = 'm' then
                         SET @Fda = (select FactorAbundamiento from factorabundamiento where IdMaterial = @IdMaterial and Estatus = 1 order by IdFactorAbundamiento desc limit 1 );
                       END IF;
                     end if;

                     set @IdOrigen = ifnull((select IdOrigen from origenes where IdOrigen = v_IdOrigen and estatus = 1),0);
                     IF @IdOrigen = 0 THEN
                         SET Text_Error = "El origen no es valido";
                         SET @ERRORI = 1;
                     END IF;
                    */


                    /*
                     * Calculo de importes
                     */

                    /******* Tarifa por material ******/

                    if ($data["TipoTarifa"] == 'm') {
                        $tarifa_material = DB::connection('sca')->select(DB::raw(" select IdTarifa, PrimerKM, KMSubsecuente, KMAdicional 
					FROM tarifas
					where idMaterial = " . $viaje_neto["0"]->IdMaterial . " and  InicioVigencia < '" . $viaje_neto["0"]->fecha . "' and IFNULL(FinVigencia,NOW()) > '" . $viaje_neto ["0"]->fecha . "' and estatus = 1"));
                        $id_tarifa = $tarifa_material["0"]->IdTarifa;
                        $t_primerkm = $tarifa_material["0"]->PrimerKM;
                        $t_subsecuente = $tarifa_material["0"]->KMSubsecuente;
                        $t_adicional = $tarifa_material["0"]->KMAdicional;
                        $tarifa_importePrimerKM = $tarifa_material["0"]->PrimerKM * $cubicacion * $rutas_activas["0"]->PrimerKm;
                        $tarifa_importeKMSubsecuentes = $tarifa_material["0"]->KMSubsecuente * $cubicacion * $rutas_activas["0"]->KmSubsecuentes;
                        $tarifa_importeKMAdicionales = $tarifa_material["0"]->KMAdicional * $cubicacion * $rutas_activas["0"]->KmAdicionales;
                    }
                    /*
                     * Tarifa por ruta y material
                     */
                    if ($data["TipoTarifa"] == 'rm') {
                        $tarifa_ruta_material = DB::connection('sca')->select(DB::raw("SELECT * FROM tarifas_ruta_material WHERE id_ruta = " . $rutas_activas["0"]->IdRuta . "
                                                        AND id_material = " . $viaje_neto["0"]->IdMaterial . "
                                                        AND tarifas_ruta_material.Estatus != 2
                                                        AND tarifas_ruta_material.inicio_vigencia <= '" . $viaje_neto["0"]->fecha . "'
                                                        AND IFNULL(tarifas_ruta_material.fin_vigencia, NOW()) >= '" . $viaje_neto["0"]->fecha . "'
                                                        AND id = " . $data["idtarifa_ruta_material"] . " "));
                        $id_tarifa = $tarifa_ruta_material["0"]->id;
                        $t_primerkm = $tarifa_ruta_material["0"]->primer_km;
                        $t_subsecuente = $tarifa_ruta_material["0"]->km_subsecuentes;
                        $t_adicional = $tarifa_ruta_material["0"]->km_adicionales;
                        $tarifa_importePrimerKM = $tarifa_ruta_material["0"]->primer_km * $cubicacion * $rutas_activas["0"]->PrimerKm;
                        $tarifa_importeKMSubsecuentes = $tarifa_ruta_material["0"]->km_subsecuentes * $cubicacion * $rutas_activas["0"]->KmSubsecuentes;
                        $tarifa_importeKMAdicionales = $tarifa_ruta_material["0"]->km_adicionales * $cubicacion * $rutas_activas["0"]->KmAdicionales;
                    }
                    if ($tarifa_importePrimerKM == 0 && $tarifa_importeKMSubsecuentes == 0) {
                        $error = "Error en la tarifa";
                    } else {
                        $tarifa_importe = $tarifa_importePrimerKM + $tarifa_importeKMSubsecuentes + $tarifa_importeKMAdicionales;
                    }
                    DB::connection('sca')
                        ->table('viajes')
                        ->insert([
                            "IdTarifa" => $id_tarifa,
                            "IdViajeNeto" => $id_viaje_neto,
                            "FechaCarga" => Carbon::now()->toDateString(),
                            "HoraCarga" => Carbon::now()->toDateTimeString(),
                            "IdProyecto" => $viaje_neto["0"]->IdProyecto,
                            "IdCamion" => $viaje_neto["0"]->IdCamion,
                            "IdMaquinaria" => 0,
                            "HorasEfectivas" => 0.00,
                            "CubicacionCamion" => $cubicacion,
                            "IdOrigen" => $viaje_neto["0"]->IdOrigen,
                            "IdSindicato" => $data["IdSindicato"],
                            "IdEmpresa" => $data["IdEmpresa"],
                            "FechaSalida" => $viaje_neto["0"]->FechaSalida,
                            "HoraSalida" => $viaje_neto["0"]->HoraSalida,
                            "IdTiro" => $viaje_neto["0"]->IdTiro,
                            "FechaLlegada" => $viaje_neto["0"]->FechaLlegada,
                            "HoraLlegada" => $viaje_neto["0"]->HoraLlegada,
                            "IdMaterial" => $viaje_neto["0"]->IdMaterial,
                            "FactorAbundamiento" => 0.00,
                            "IdChecador" => auth()->user()->idusuario,
                            "Creo" => $viaje_neto["0"]->Creo,
                            "TiempoViaje" => $this->tiempoViaje($viaje_neto["0"]->FechaSalida . " " . $viaje_neto["0"]->HoraSalida, $viaje_neto["0"]->FechaLlegada . " " . $viaje_neto["0"]->HoraLlegada),
                            "IdRuta" => $rutas_activas["0"]->IdRuta,
                            "Distancia" => $rutas_activas["0"]->TotalKM,
                            "TPrimerKM" => $t_primerkm,
                            "TKMSubsecuente" => $t_subsecuente,
                            "TKMAdicional" => $t_adicional,
                            "VolumenPrimerKM" => $ruta_volumen_primerKM,
                            "VolumenKMSubsecuentes" => $ruta_volumen_kmsubsecuentes,
                            "VolumenKMAdicionales" => $ruta_volumen_kmadicionales,
                            "Volumen" => $ruta_volumen,
                            "ImportePrimerKM" => $tarifa_importePrimerKM,
                            "ImporteKMSubsecuentes" => $tarifa_importeKMSubsecuentes,
                            "ImporteKMAdicionales" => $tarifa_importeKMAdicionales,
                            "Importe" => $tarifa_importe,
                            "Observaciones" => $viaje_neto["0"]->Observaciones != '' ? $viaje_neto["0"]->Observaciones : NULL,
                            "TipoTarifa" => $data["TipoTarifa"],
                            "Estatus" => $viaje_neto["0"]->Estatus,
                            "Code" => $viaje_neto["0"]->Code,
                            "Tara" => $data["Tara"],
                            "Bruto" => $data["Bruto"],
                            "Peso" => $data["Tara"] - $data["Bruto"]
                        ]);
                }
                if ($data["Accion"] == 0) {
                    DB::connection('sca')
                        ->table('viajesrechazados')
                        ->insert([
                            "IdViajeNeto" => $id_viaje_neto,
                            "FechaRechazo" => Carbon::now()->toDateString(),
                            "HoraRechazo" => Carbon::now()->toDateTimeString(),
                            "IdProyecto" => $viaje_neto["0"]->IdProyecto,
                            "IdCamion" => $viaje_neto["0"]->IdCamion,
                            "IdMaquinaria" => 0,
                            "HorasEfectivas" => '0.00',
                            "CubicacionCamion" => $cubicacion,
                            "IdOrigen" => $viaje_neto["0"]->IdOrigen,
                            "FechaSalida" => $viaje_neto["0"]->FechaSalida,
                            "HoraSalida" => $viaje_neto["0"]->HoraSalida,
                            "IdTiro" => $viaje_neto["0"]->IdTiro,
                            "FechaLlegada" => $viaje_neto["0"]->FechaLlegada,
                            "HoraLlegada" => $viaje_neto["0"]->HoraLlegada,
                            "IdMaterial" => $viaje_neto["0"]->IdMaterial,
                            "FactorAbundamiento" => 0.00,
                            "IdChecador" => auth()->user()->idusuario,
                            "Creo" => $viaje_neto["0"]->Creo,
                            "TiempoViaje" => $this->tiempoViaje($viaje_neto["0"]->FechaSalida . " " . $viaje_neto["0"]->HoraSalida, $viaje_neto["0"]->FechaLlegada . " " . $viaje_neto["0"]->HoraLlegada),
                            "Estatus" => $viaje_neto["0"]->Estatus
                        ]);
                }
                DB::connection('sca')->commit();
                $viaje = DB::connection('sca')->select(DB::raw("select count(*) from viajes where IdViajeneto = " . $id_viaje_neto . " "));
                $existe = DB::connection('sca')->select(DB::raw("select count(*) from viajesrechazados where IdViajeneto = " . $id_viaje_neto . " "));
                if ($viaje != [] || $existe != []) {
                    $msg = $data['Accion'] == 1 ? 'Viaje validado exitosamente' : 'Viaje Rechazado exitosamente';
                    $tipo = $data['Accion'] == 1 ? 'success' : 'info';
                }
                /*
                 * Validar los datos a editar en el viaje neto...
                 */
                if ($data["Accion"] == 1 && $viaje != []) {
                    $viajeneto = ViajeNeto::find($id_viaje_neto);
                    $estado = $viaje_neto["0"]->Estatus;
                    $uss = auth()->user()->idusuario;
                    $viajeneto->update(['Estatus' => $estado+1,'Modifico' => $uss]);

                    if ($this->CubicacionCamion != $cubicacion) {
                        DB::connection('sca')
                            ->table('cambio_cubicacion')
                            ->insert([
                                "IdViajeNeto" => $id_viaje_neto,
                                "FechaRegistro" => Carbon::now()->toDateTimeString(),
                                "VolumenViejo" => $this->CubicacionCamion,
                                "VolumenNuevo" => $cubicacion
                            ]);
                    }
                    DB::connection('sca')->commit();
                }
                return ['message' => $msg,
                    'tipo' => $tipo];
            } else {
                $tipo = 'error';
                return ['message' => $error,
                    'tipo' => $tipo];
            }
        }catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
       /* DB::connection('sca')->beginTransaction();
        try {
            $statement ="call sca_sp_registra_viaje_fda_v2("
                .$data["Accion"].","<   q11q
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
       */
    }
    public function tiempoViaje($fechaInicial, $fechaFinal){

        $fecha1 = new DateTime($fechaInicial);//fecha inicial
        $fecha2 = new DateTime($fechaFinal);//fecha de cierre
        $suma = 0;

        $intervalo = $fecha1->diff($fecha2);

        if($intervalo->format("%H") != "0"){
            $suma = $suma + ($intervalo->format("%H")*60);
        }
        if($intervalo->format("%i") != "0"){
            $suma = $suma + $intervalo->format("%i");
        }
        if($intervalo->format("%s") != "0"){
            $suma = $suma + ($intervalo->format("%s")/60);
        }

        return round($suma, 2);
    }

    public function poner_pagable($request){
        if(str_replace(" ", "", $request->get("motivo"))==""){
            throw new \Exception("Indique el motivo para permitir el pago del viaje en conflicto");
        }

        ViajeNetoConflictoPagable::create([
            "idviaje_neto"=>$this->IdViajeNeto,
            "idconflicto"=>$request->IdConflicto,
            "motivo"=>$request->motivo,
            "aprobo_pago"=>auth()->user()->idusuario
        ]);
    }
    public function modificar($request) {
        $data = $request->get('data');
        $viaje_aprobado = $this->viaje;
        if($viaje_aprobado)
            throw new \Exception("El viaje no puede ser modificado porque ya se encuentra validado.");

        DB::connection('sca')->beginTransaction();
        try {

            if($this->IdEmpresa != $data['IdEmpresa']){
                DB::connection('sca')->table('cambio_empresa')->insert([
                    'IdViajeNeto'       => $this->IdViajeNeto,
                    'IdEmpresaAnterior' => $this->IdEmpresa,
                    'IdEmpresaNuevo'    => $data['IdEmpresa'],
                    'FechaRegistro'     => Carbon::now()->toDateTimeString(),
                    'Registro'          => auth()->user()->idusuario
                ]);
                $this->IdEmpresa = $data['IdEmpresa'];
            }

            if($this->IdSindicato != $data['IdSindicato']) {
                DB::connection('sca')->table('cambio_sindicato')->insert([
                    'IdViajeNeto'       => $this->IdViajeNeto,
                    'IdSindicatoAnterior' => $this->IdSindicato,
                    'IdSindicatoNuevo'    => $data['IdSindicato'],
                    'FechaRegistro'     => Carbon::now()->toDateTimeString(),
                    'Registro'          => auth()->user()->idusuario
                ]);
                $this->IdSindicato = $data['IdSindicato'];
            }

            if($this->IdMaterial != $data['IdMaterial']) {
                DB::connection('sca')->table('cambio_material')->insert([
                    'IdViajeNeto'        => $this->IdViajeNeto ,
                    'IdMaterialAnterior' => $this->IdMaterial,
                    'IdMaterialNuevo'    => $data['IdMaterial'],
                    'FechaRegistro'      => Carbon::now()->toDateTimeString(),
                    'Registro'           => auth()->user()->idusuario
                ]);
                $this->IdMaterial = $data['IdMaterial'];
            }

            if($this->IdTiro != $data['IdTiro']) {
                DB::connection('sca')->table('cambio_tiro')->insert([
                    'IdViajeNeto'    => $this->IdViajeNeto ,
                    'IdTiroAnterior' => $this->IdTiro,
                    'IdTiroNuevo'    => $data['IdTiro'],
                    'FechaRegistro'  => Carbon::now()->toDateTimeString(),
                    'Registro'       => auth()->user()->idusuario
                ]);
                $this->IdTiro = $data['IdTiro'];
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
                    'IdTiro' => $this->IdTiro,
                    'Tiro' => $this->tiro->Descripcion,
                    'Material' => $this->material->Descripcion,
                    'IdMaterial' => $this->IdMaterial,
                    'IdSindicato' => $this->IdSindicato,
                    'Sindicato' => (String) $this->sindicato,
                    'IdEmpresa' => $this->IdEmpresa,
                    'Empresa' => (String) $this->empresa
                ]
            ];
        } catch (Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }

    public function viaje() {
        return $this->hasOne(Viaje::class, 'IdViajeNeto');
    }
    public function conflicto_pagable() {
        return $this->hasOne(ViajeNetoConflictoPagable::class, 'idviaje_neto');
    }

    public function viaje_rechazado() {
        return $this->hasOne(ViajeRechazado::class, 'IdViajeNeto');
    }

    public function empresa () {
        return $this->belongsTo(Empresa::class, 'IdEmpresa');
    }

    public function sindicato () {
        return $this->belongsTo(Sindicato::class, 'IdSindicato');
    }

    public function getSindicatoConciliadoAttribute() {
        if($this->viaje) {
            if($this->viaje->conciliacionDetalles->where('estado', 1)->first()) {
                $detalle = $this->viaje->conciliacionDetalles->where('estado', 1)->first();
                if($detalle->conciliacion->sinicato) {
                    return (String) $detalle->conciliacion->sindicato;
                }
                return '';
            }
            return '';
        }
        return '';
    }

    public function getTipoAttribute() {
        return in_array($this->Estatus, ['0', '1']) ? 'APLICACIÓN MÓVIL' : 'MANUAL';
    }

    public function getEstadoAttribute() {
        switch ($this->Estatus) {
            case 0 :
                return "PENDIENTE DE VALIDAR";
                break;
            case 1:
                if(count($this->viaje)) {
                    return "VALIDADO";
                } else {
                    return "NO VALIDADO (DENEGADO)";
                }
                break;
            case 20:
                return "PENDIENTE DE VALIDAR";
                break;
            case 21:
                if(count($this->viaje)) {
                    return "VALIDADO";
                } else {
                    return "NO VALIDADO (DENEGADO)";
                }
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

    public function getRegistroAttribute(){
        $creo = $this->Creo;
        if(is_numeric($creo)){
            if(!count($this->usuario_registro)) {
                return '';
            }
            $registro = $this->usuario_registro->present()->NombreCompleto;
            return $registro;
        }else{

            return $creo;
        }
    }

    public function getRegistroPrimerToqueAttribute(){
        $creo = $this->CreoPrimerToque;
        if(is_numeric($creo)){
            if(!count($this->usuario_registro_primer_toque)) {
                dd($this->CreoPrimerToque);
            }
            $registro = $this->usuario_registro_primer_toque->present()->NombreCompleto;
            return $registro;
        }else{
            return $creo;
        }
    }

    public function getAutorizoAttribute(){
        return $this->Aprobo ? User::find($this->Aprobo)->present()->NombreCompleto : '';
    }

    public function getValidoAttribute(){
        $valido = $this->attribute['Valido'];
        return $valido ? User::find($valido)->present()->NombreCompleto : '';
    }

    public function getRechazoAttribute(){
        $rechazo = $this->attribute['Rechazo'];
        return $rechazo ? User::find($rechazo)->present()->NombreCompleto : '';
    }

    public function getDenegoAttribute(){
        $denego = $this->attribute['Denego'];
        return $denego ? User::find($denego)->present()->NombreCompleto : '';
    }

    public function usuario_registro(){
        return  $this->belongsTo(User::class, 'Creo');
    }

    public function usuario_registro_primer_toque(){
        return  $this->belongsTo(User::class, 'CreoPrimerToque');
    }

    public static function scopeManualesAutorizados($query) {
        return $query->select(DB::raw('viajesnetos.*'))
            ->where('viajesnetos.Estatus', 20);
    }

    public static function scopeManualesRechazados($query) {
        return $query->select(DB::raw('viajesnetos.*'))
            ->where('viajesnetos.Estatus', 22);
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function scopeManualesValidados($query) {
        return $query->select(DB::raw('viajesnetos.*'))->leftJoin('viajes', 'viajesnetos.IdViajeNeto', '=', 'viajes.IdViajeNeto')
            ->where(function($query){
                $query->whereNotNull('viajes.IdViaje')
                    ->where('viajesnetos.Estatus', 21);
            });
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function scopeManualesDenegados($query) {
        return $query->select(DB::raw('viajesnetos.*'))->leftJoin('viajesrechazados', 'viajesnetos.IdViajeNeto', '=', 'viajesrechazados.IdViajeNeto')
            ->where(function ($query) {
                $query->whereNotNull('viajesrechazados.IdViajeRechazado')
                    ->where('viajesnetos.Estatus', 21);
            });
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function scopeMovilesDenegados($query) {
        return $query->select(DB::raw('viajesnetos.*'))->leftJoin('viajesrechazados', 'viajesnetos.IdViajeNeto', '=', 'viajesrechazados.IdViajeNeto')
            ->where(function ($query) {
                $query->whereNotNull('viajesrechazados.IdViajeRechazado')
                    ->where('viajesnetos.Estatus', 1);
            });
    }

    public function scopeDenegados($query) {
        return $query->leftJoin('viajesrechazados', 'viajesnetos.IdViajeNeto', '=', 'viajesrechazados.IdViajeNeto')
            ->where(function ($query) {
                $query->whereNotNull('viajesrechazados.IdViajeRechazado')
                    ->whereIn('viajesnetos.Estatus', [1, 11, 21]);
            });
    }

    public static function scopeMovilesValidados($query) {
        return $query->select(DB::raw('viajesnetos.*'))->leftJoin('viajes', 'viajesnetos.IdViajeNeto', '=', 'viajes.IdViajeNeto')
            ->where(function($query){
                $query->whereNotNull('viajes.IdViaje')
                    ->where('viajesnetos.Estatus', 1);
            });
    }

    public static function scopeMovilesAutorizados($query) {
        return $query->select(DB::raw('viajesnetos.*'))
            ->where('viajesnetos.Estatus', 0);
    }

    public static function scopeManuales($query){
        return $query->whereIn('viajesnetos.Estatus', [20,21,22,29]);
    }

    public function scopeMoviles($query) {
        return $query->whereIn('viajesnetos.Estatus', [0,1]);
    }

    public function scopeAutorizados($query) {
        return $query->whereIn('viajesnetos.Estatus', [0,20]);
    }

    public static function scopeEnConflicto($query) {
        return $query->join('conflictos_entre_viajes_detalle_ultimo', 'viajesnetos.IdViajeNeto', '=', 'conflictos_entre_viajes_detalle_ultimo.idviaje_neto');
    }

    public function conflicto_entre_viajes(){
        return $this->hasMany(ConflictoEntreViajesDetalle::class, "idviaje_neto", "IdViajeNeto");
    }
    public function getConflictoAttribute(){
        #Máximo Id de conflicto
        $resultado = DB::connection('sca')->select(DB::raw('select max(idconflicto) as idconflicto from conflictos_entre_viajes_detalle
        where idviaje_neto = '.$this->IdViajeNeto));
        $idconflicto = $resultado[0]->idconflicto;
        $conflicto = Conflictos\ConflictoEntreViajes::find($idconflicto);
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
                $codigos.= "\n".$detalle->viaje_neto->Code.str_repeat("\t",(20-strlen($detalle->viaje_neto->Code))) . "\t[Salida: ".$detalle->viaje_neto->timestamp_salida."] [Llegada: ".$detalle->viaje_neto->timestamp_llegada."]";
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
                $codigos.= "<tr><td>".$detalle->viaje_neto->Code. "</td><td>".$detalle->viaje_neto->timestamp_salida->format("d-m-Y H:i:s")."</td><td>".$detalle->viaje_neto->timestamp_llegada->format("d-m-Y H:i:s")."</td></tr>";
                // }
            }
            $codigos.="</table>";
            $codigos.= "<br>Los viajes en conflicto deben ser presentados a aclaración para su cobro.";
            return $codigos;
        }else{
            return "";
        }
    }

    public function getTimestampLlegadaAttribute(){
        $timestampLlegada = Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaLlegada.' '.$this->HoraLlegada);
        return $timestampLlegada;
    }
    public function getTimestampCargaAttribute(){
        $timestampLlegada = Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaCarga.' '.$this->HoraCarga);
        return $timestampLlegada;
    }
    public function getTimestampSalidaAttribute(){
        $timestampLlegada = Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaSalida.' '.$this->HoraSalida);
        return $timestampLlegada;
    }
    public function getTimestampAproboAttribute(){
        $timestampLlegada = $this->FechaHoraAprobacion->format("d-m-Y h:i:s");
        //Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaSalida.' '.$this->HoraSalida);
        return $timestampLlegada;
    }
    public function getTimestampRechazoAttribute(){
        $timestampLlegada = $this->FechaHoraRechazo->format("d-m-Y h:i:s");
        //Carbon::createFromFormat('Y-m-d H:i:s', $this->FechaSalida.' '.$this->HoraSalida);
        return $timestampLlegada;
    }
    public function getUsuarioAproboAttribute(){
        $usuario = User::find($this->Aprobo);
        if($usuario){
            return $usuario->present()->nombreCompleto;
        }
        return "";
    }

    public function getUsuarioRechazoAttribute(){
        $usuario = User::find($this->Rechazo);
        if($usuario){
            return $usuario->present()->nombreCompleto;
        }
        return "";
    }

    public static function scopeCorte(){
        return DB::connection('sca')->table('viajesnetos')
                ->leftJoin('corte_detalle', 'viajesnetos.IdViajeNeto', '=', 'corte_detalle.id_viajeneto')
                ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
                ->leftJoin('origenes', 'viajesnetos.IdOrigen', '=', 'origenes.IdOrigen')
                ->leftJoin('tiros', 'viajesnetos.IdTiro', '=', 'tiros.IdTiro')
                ->leftJoin('materiales', 'viajesnetos.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario as primer_toque', 'viajesnetos.CreoPrimerToque', '=', 'primer_toque.idusuario')
            ->leftJoin('igh.usuario as user_registro', 'viajesnetos.Creo', '=', 'user_registro.idusuario')
                ->whereNull('corte_detalle.id_viajeneto')
            ->where('viajesnetos.Creo', auth()->user()->idusuario)
            ->orderBy('viajesnetos.IdViajeNeto', 'DESC')
            ->select(
                "viajesnetos.*",
                "camiones.Economico as camion",
                "viajesnetos.Code as codigo",
                DB::raw("CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) as timestamp_llegada"),
                "origenes.Descripcion as origen",
                "tiros.Descripcion as tiro",
                "materiales.Descripcion as material",
                "viajesnetos.CubicacionCamion as cubicacion",
                DB::raw("CONCAT(primer_toque.nombre, ' ', primer_toque.apaterno, ' ' , primer_toque.amaterno) as registro_primer_toque"),
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ' , user_registro.amaterno) as registro"),
                "viajesnetos.Estatus as estatus"
            );
    }

    public static function scopeCorteEdit($id_corte){
        return DB::connection('sca')->table('viajesnetos')
            ->leftJoin('corte_detalle', 'viajesnetos.IdViajeNeto', '=', 'corte_detalle.id_viajeneto')
            ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('origenes', 'viajesnetos.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('tiros', 'viajesnetos.IdTiro', '=', 'tiros.IdTiro')
            ->leftJoin('materiales', 'viajesnetos.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario as primer_toque', 'viajesnetos.CreoPrimerToque', '=', 'primer_toque.idusuario')
            ->leftJoin('igh.usuario as user_registro', 'viajesnetos.Creo', '=', 'user_registro.idusuario')
            ->leftJoin('corte_cambios', 'viajesnetos.IdViajeNeto', '=', 'corte_cambios.id_viajeneto')
            ->leftJoin('origenes as origen_nuevo', 'corte_cambios.id_origen_nuevo', '=', 'origen_nuevo.IdOrigen')
            ->leftJoin('tiros as tiro_nuevo', 'corte_cambios.id_tiro_nuevo', '=', 'tiro_nuevo.IdTiro')
            ->leftJoin('materiales as material_nuevo', 'corte_cambios.id_material_nuevo', '=', 'material_nuevo.IdMaterial')
            ->where('viajesnetos.Creo', auth()->user()->idusuario)
            ->where('corte_detalle.id_corte', '=', $id_corte)
            ->orderBy('viajesnetos.IdViajeNeto', 'DESC')
            ->select(
                "viajesnetos.*",
                "camiones.Economico as camion",
                "viajesnetos.Code as codigo",
                DB::raw("CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) as timestamp_llegada"),
                "origenes.Descripcion as origen",
                "tiros.Descripcion as tiro",
                "materiales.Descripcion as material",
                DB::raw("CONCAT(primer_toque.nombre, ' ', primer_toque.apaterno, ' ' , primer_toque.amaterno) as registro_primer_toque"),
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ' , user_registro.amaterno) as registro"),
                DB::raw("IF(viajesnetos.Estatus = 0 OR viajesnetos.Estatus = 1, 'APLICACIÓN MOVIL', 'MANUAL') as tipo"),
                DB::raw("IF(viajesnetos.Estatus = 0 OR viajesnetos.Estatus = 1, false, true) as manual"),
                DB::raw("IF(corte_cambios.id is not null, true, false) as corte_cambio"),
                "corte_cambios.cubicacion_nueva as CubicacionCamionNueva",
                "corte_cambios.id_material_nuevo as IdMaterialNuevo",
                "corte_cambios.id_origen_nuevo as IdOrigenNuevo",
                "corte_cambios.id_tiro_nuevo as IdTiroNuevo",
                "corte_cambios.justificacion as justificacion",
                "origen_nuevo.Descripcion as origen_nuevo",
                "tiro_nuevo.Descripcion as tiro_nuevo",
                "material_nuevo.Descripcion as material_nuevo",
                DB::raw("IF(corte_detalle.estatus = 2, true, false) as confirmed")
            );
    }

    public function corte_cambio() {
        return $this->hasOne(CorteCambio::class, 'id_viajeneto', 'IdViajeNeto');
    }

    public function corte_detalle() {
        return $this->hasOne(CorteDetalle::class, 'id_viajeneto');

    }

    public static function scopeReporte($query) {
        return $query
            ->leftJoin('igh.usuario as user_autorizo','viajesnetos.Aprobo','=','user_autorizo.idusuario')
            ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('materiales','viajesnetos.IdMAterial','=','materiales.IdMAterial')
            ->leftJoin('origenes','viajesnetos.IdOrigen','=','origenes.IdOrigen')
            ->leftJoin('igh.usuario as user_registro','viajesnetos.Creo','=','user_registro.idusuario')
            ->leftJoin('igh.usuario as user_primer_toque','viajesnetos.CreoPrimerToque','=','user_primer_toque.idusuario')
            ->leftJoin('tiros','viajesnetos.IdTiro','=','tiros.IdTiro')
            ->leftJoin('conflictos_entre_viajes_detalle_ultimo as conflicto', 'viajesnetos.IdViajeNeto', '=', 'conflicto.idviaje_neto')
            ->leftJoin('viajes_netos_conflictos_pagables as conflictos_pagables','viajesnetos.IdViajeNeto','=','conflictos_pagables.idviaje_neto')
            ->leftJoin('viajes as v','viajesnetos.IdViajeNeto','=','v.IdViajeNeto')
            ->leftJoin('igh.usuario as user_valido','v.Creo','=','user_valido.idusuario')
            ->leftJoin('igh.usuario as user_aprobo_pago','conflictos_pagables.aprobo_pago','=','user_aprobo_pago.idusuario')
            ->leftJoin('tarifas', DB::raw("(tarifas.IdMaterial=materiales.IdMaterial AND tarifas.Estatus=1 and tarifas.InicioVigencia < viajesnetos.FechaLlegada and IFNULL(tarifas.FinVigencia,NOW())"), '>' , DB::raw("viajesnetos.FechaLlegada)"))
            /*->leftJoin('rutas', function ($join) {
                $join->on(DB::raw("rutas.IdOrigen = viajesnetos.IdOrigen and rutas.IdTiro"),  '=', 'viajesnetos.IdTiro');
            })*/
            ->leftJoin(DB::raw("(SELECT * FROM rutas group by IdOrigen, IdTiro) as rutas"), DB::raw("(viajesnetos.IdOrigen=rutas.IdOrigen AND viajesnetos.IdTiro"), '=', DB::raw("rutas.IdTiro)"))
            ->leftJoin('empresas as empresas_viajes', 'v.IdEmpresa', '=', 'empresas_viajes.IdEmpresa')
            ->leftJoin('empresas as empresas_viajesnetos', 'viajesnetos.IdEmpresa', '=', 'empresas_viajesnetos.IdEmpresa')
            ->leftJoin('empresas as empresas_camiones', 'camiones.IdEmpresa', '=', 'empresas_camiones.IdEmpresa')
            ->leftJoin('sindicatos as sindicatos_viajes', 'v.IdSindicato', '=', 'sindicatos_viajes.IdSindicato')
            ->leftJoin('sindicatos as sindicatos_viajesnetos', 'viajesnetos.IdSindicato', '=', 'sindicatos_viajesnetos.IdSindicato')
            ->leftJoin('sindicatos as sindicatos_camiones', 'camiones.IdSindicato', '=', 'sindicatos_camiones.IdSindicato')
            ->leftJoin('conciliacion_detalle as cd', DB::raw("viajesnetos.IdViajeNeto = cd.idviaje_neto AND cd.estado"), '=', DB::raw("1"))
            ->leftJoin('conciliacion as c', 'cd.idconciliacion', '=', 'c.idconciliacion')
            ->leftJoin('igh.usuario as user_concilio', 'c.IdRegistro', '=', 'user_concilio.idusuario')
                ->addSelect(
                "viajesnetos.IdViajeNeto as id",
                DB::raw("IF(viajesnetos.Aprobo is not null, CONCAT(user_autorizo.nombre, ' ', user_autorizo.apaterno, ' ', user_autorizo.amaterno), '') as autorizo"),
                "camiones.Economico as camion",
                DB::raw("IF(viajesnetos.CubicacionCamion <= 8, camiones.CubicacionParaPago, viajesnetos.CubicacionCamion) as cubicacion"),
                "viajesnetos.CubicacionCamion as cubicacion",
                DB::raw("(CASE viajesnetos.Estatus when 0 then 'PENDIENTE DE VALIDAR'
                    when 1 then (IF(v.IdViaje is null, 'NO VALIDADO (DENEGADO)', 'VALIDADO')) 
                    when 20 then 'PENDIENTE DE VALIDAR'
                    when 21 then (IF(v.IdViaje is null, 'NO VALIDADO (DENEGADO)', 'VALIDADO'))
                    when 22 then 'NO AUTORIZADO (RECHAZADO)' 
                    when 29 then 'CARGADO'
                    END) as estado"),
                "materiales.Descripcion as material",
                "origenes.Descripcion as origen",
                DB::raw("IF(user_registro.idusuario is not null, CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno), viajesnetos.Creo) as registro"),
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as registro_primer_toque"),
                DB::raw("IF(viajesnetos.Estatus = 0 OR viajesnetos.Estatus = 1, 'APLICACIÓN MOVIL', 'MANUAL') as tipo"),
                "tiros.Descripcion as tiro",
                DB::raw("IF(v.Importe is not null, v.Importe, 
                IF(viajesnetos.CubicacionCamion <= 8,
                ((tarifas.PrimerKM*1*camiones.CubicacionParaPago)+(tarifas.KMSubsecuente*rutas.KmSubsecuentes*camiones.CubicacionParaPago)+(tarifas.KMAdicional*rutas.KmAdicionales*camiones.CubicacionParaPago)) 
                , 
                ((tarifas.PrimerKM*1*viajesnetos.CubicacionCamion)+(tarifas.KMSubsecuente*rutas.KmSubsecuentes*viajesnetos.CubicacionCamion)+(tarifas.KMAdicional*rutas.KmAdicionales*viajesnetos.CubicacionCamion)) 
                )) as importe"),
                DB::raw("CONCAT(user_valido.nombre, ' ', user_valido.apaterno, ' ', user_valido.amaterno) as valido"),
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
                "sindicatos_camiones.NombreCorto as sindicato_camion",
                DB::raw("group_concat(c.idconciliacion) as id_conciliacion"),
                DB::raw("group_concat(CONCAT(user_concilio.nombre, ' ', user_concilio.apaterno, ' ', user_concilio.amaterno)) as concilio"),
                DB::raw("group_concat(c.fecha_conciliacion) as fecha_conciliacion"),
                DB::raw("CONCAT(viajesnetos.FechaCarga, ' ', viajesnetos.HoraCarga) as fecha_hora_carga")
            )
            ->groupBy('viajesnetos.IdViajeNeto');
    }


    public static function validandoCierre($FechaLlegada){
        /* Bloqueo de cierre de periodo
            1 : Cierre de periodo
            0 : Periodo abierto.
        */
        $fecha = Carbon::createFromFormat('Y-m-d', $FechaLlegada);
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
}
