<?php

namespace App\Models\Conciliacion;

use App\Models\Empresa;
use App\Models\Ruta;
use App\Models\Sindicato;
use App\Models\ValidacionCierrePeriodo;
use App\Models\Viaje;
use App\Presenters\ModelPresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\User;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Return_;
use App\Models\Conciliacion\ConciliacionDetalleNoConciliado;
use App\Facades\Context;
class Conciliacion extends Model
{
    use \Laracasts\Presenter\PresentableTrait;
    
    const FECHA_HISTORICO = 20170409;
    const HOLGURA_IMPORTE = 50000;
    const HOLGURA_VOLUMEN = 175;

    protected $connection = 'sca';
    protected $table = 'conciliacion';
    protected $primaryKey = 'idconciliacion';
    public $timestamps = false;

    protected $fillable = [
        'fecha_conciliacion',
        'idsindicato',
        'idempresa',
        'fecha_inicial',
        'fecha_final',
        'timestamp',
        'estado',
        'IdRegistro',
        'Folio',
        'ImportePagado',
        'VolumenPagado'
    ];
    protected $dates = ['timestamp','fecha_conciliacion', 'FechaHoraCierre', 'FechaHoraAprobacion'];
    protected $presenter = ModelPresenter::class;

    public function rutas()
    {
        return $this->belongsToMany(Ruta::class, 'conciliacion_rutas', 'idconciliacion', 'IdRuta');
    }

    public function conciliacionDetalles()
    {
        return $this->hasMany(ConciliacionDetalle::class, 'idconciliacion');
    }
    
    public function conciliacionDetallesNoConciliados()
    {
        return $this->hasMany(ConciliacionDetalleNoConciliado::class, 'idconciliacion');
    }

    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class, 'idsindicato');
    }
    public function getConciliacionDetallesNoConciliadosPDFAttribute(){
        $detalle_pdf = [];
       foreach($this->conciliacionDetallesNoConciliados as $detalle){
           if($detalle->idmotivo != 7){
               $detalle_pdf[] = $detalle;
           }
       }
        return $detalle_pdf;
    }
    public function materiales()
    {
        return DB::connection($this->connection)->select(DB::raw('
            SELECT viajes.IdMaterial, materiales.Descripcion as material 
              FROM (viajes viajes 
                INNER JOIN materiales materiales
                  ON (viajes.IdMaterial = materiales.IdMaterial))
                RIGHT OUTER JOIN conciliacion_detalle conciliacion_detalle
				  ON (conciliacion_detalle.idviaje = viajes.IdViaje)
			  WHERE (conciliacion_detalle.idconciliacion = ' . $this->idconciliacion . ') 
			  AND(conciliacion_detalle.estado = 1)
			  GROUP BY materiales.Descripcion
			  ORDER BY materiales.Descripcion ASC;'));
    }

    public function materiales_manuales() {
        return DB::connection($this->connection)->select(DB::raw('
            SELECT viajes.IdMaterial, materiales.Descripcion as material 
              FROM (viajes viajes 
                INNER JOIN materiales materiales
                  ON (viajes.IdMaterial = materiales.IdMaterial))
                RIGHT OUTER JOIN conciliacion_detalle conciliacion_detalle
				  ON (conciliacion_detalle.idviaje = viajes.IdViaje)
			  WHERE (conciliacion_detalle.idconciliacion = ' . $this->idconciliacion . ') 
			  AND(conciliacion_detalle.estado = 1)
			  AND(viajes.Estatus = 20)
			  GROUP BY materiales.Descripcion
			  ORDER BY materiales.Descripcion ASC;'));
    }

    public function materiales_moviles() {
        return DB::connection($this->connection)->select(DB::raw('
            SELECT viajes.IdMaterial, materiales.Descripcion as material 
              FROM (viajes viajes 
                INNER JOIN materiales materiales
                  ON (viajes.IdMaterial = materiales.IdMaterial))
                RIGHT OUTER JOIN conciliacion_detalle conciliacion_detalle
				  ON (conciliacion_detalle.idviaje = viajes.IdViaje)
			  WHERE (conciliacion_detalle.idconciliacion = ' . $this->idconciliacion . ') 
			  AND(conciliacion_detalle.estado = 1)
			  AND(viajes.Estatus = 0)
			  GROUP BY materiales.Descripcion
			  ORDER BY materiales.Descripcion ASC;'));
    }

    public function viajes()
    {
        $viajes = new Collection();
        foreach ($this->conciliacionDetalles->where('estado', 1) as $cd) {
            $viajes->push($cd->viaje);
        }
        return $viajes;
    }

    public function viajes_manuales()
    {
        $viajes = new Collection();
        foreach ($this->conciliacionDetalles->where('estado', 1) as $cd) {
            if($cd->viaje->Estatus == 20) {
                $viajes->push($cd->viaje);
            }
        }
        return $viajes;
    }

    public function viajes_moviles()
    {
        $viajes = new Collection();
        foreach ($this->conciliacionDetalles->where('estado', 1) as $cd) {
            if($cd->viaje->Estatus == 0) {
                $viajes->push($cd->viaje);
            }
        }
        return $viajes;
    }

    public function camiones()
    {
        return DB::connection($this->connection)->select(DB::raw('
            SELECT viajes.IdCamion, camiones.Economico as Economico 
              FROM (viajes viajes 
                INNER JOIN camiones camiones
                  ON (viajes.IdCamion = camiones.IdCamion))
                RIGHT OUTER JOIN conciliacion_detalle conciliacion_detalle
				  ON (conciliacion_detalle.idviaje = viajes.IdViaje)
			  WHERE (conciliacion_detalle.idconciliacion = ' . $this->idconciliacion . ') 
			  AND(conciliacion_detalle.estado = 1)
			  GROUP BY camiones.Economico
			  ORDER BY camiones.Economico ASC;'));
    }

    public function camiones_moviles()
    {
        return DB::connection($this->connection)->select(DB::raw('
            SELECT viajes.IdCamion, camiones.Economico as Economico 
              FROM (viajes viajes 
                INNER JOIN camiones camiones
                  ON (viajes.IdCamion = camiones.IdCamion))
                RIGHT OUTER JOIN conciliacion_detalle conciliacion_detalle
				  ON (conciliacion_detalle.idviaje = viajes.IdViaje)
			  WHERE (conciliacion_detalle.idconciliacion = ' . $this->idconciliacion . ') 
			  AND(conciliacion_detalle.estado = 1)
			  AND(viajes.Estatus = 0)
			  GROUP BY camiones.Economico
			  ORDER BY camiones.Economico ASC;'));
    }

    public function camiones_manuales()
    {
        return DB::connection($this->connection)->select(DB::raw('
            SELECT viajes.IdCamion, camiones.Economico as Economico 
              FROM (viajes viajes 
                INNER JOIN camiones camiones
                  ON (viajes.IdCamion = camiones.IdCamion))
                RIGHT OUTER JOIN conciliacion_detalle conciliacion_detalle
				  ON (conciliacion_detalle.idviaje = viajes.IdViaje)
			  WHERE (conciliacion_detalle.idconciliacion = ' . $this->idconciliacion . ') 
			  AND(conciliacion_detalle.estado = 1)
			  AND(viajes.Estatus = 20)
			  GROUP BY camiones.Economico
			  ORDER BY camiones.Economico ASC;'));
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'idempresa');

    }

    public function partidas()
    {
        return $this->hasMany(ConciliacionDetalle::class, 'idconciliacion', 'idconciliacion');
    }

    public function getVolumenAttribute()
    {
        $results = DB::connection("sca")->select("select sum(CubicacionCamion) as Volumen "
            . "from conciliacion "
            . "left join conciliacion_detalle on conciliacion.idconciliacion = conciliacion_detalle.idconciliacion "
            . "left join viajes on conciliacion_detalle.idviaje = viajes.IdViaje where conciliacion.idconciliacion = " . $this->idconciliacion . " "
            . "and conciliacion_detalle.estado = 1 "
            . "group by conciliacion.idconciliacion limit 1");
        return $results ? $results[0]->Volumen : 0;
    }

    public function getImporteAttribute()
    {
        $results = DB::connection("sca")->select("select sum(Importe) as Importe "
            . "from conciliacion "
            . "left join conciliacion_detalle on conciliacion.idconciliacion = conciliacion_detalle.idconciliacion "
            . "left join viajes on conciliacion_detalle.idviaje = viajes.IdViaje where conciliacion.idconciliacion = " . $this->idconciliacion . " "
            . "and conciliacion_detalle.estado = 1 "
            . "group by conciliacion.idconciliacion limit 1");
        return $results ? $results[0]->Importe : 0;
    }
    
    public function getImportePagadoFAttribute(){
        return number_format($this->ImportePagado, 2, ".",",");
    }
    
    public function getImportePagadoAlertAttribute(){
        if($this->es_historico && !($this->ImportePagado >0) ){
            return "Pendiente";
        }else{
            return $this->importe_pagado_f;
        }
    }
    public function getVolumenPagadoAlertAttribute(){
        if($this->es_historico && !($this->VolumenPagado >0) ){
            return "Pendiente";
        }else{
            return $this->volumen_pagado_f;
        }
    }
    
    public function getVolumenPagadoFAttribute(){
        return number_format($this->VolumenPagado, 2, ".",",");
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, "IdRegistro");
    }

    public function registro()
    {
        return $this->belongsTo(User::class, "IdRegistro");
    }

    public function cerro()
    {
        return $this->belongsTo(User::class, "IdCerro");
    }

    public function aprobo()
    {
        return $this->belongsTo(User::class, "IdAprobo");
    }

    public function getVolumenFAttribute()
    {

        return number_format($this->volumen, 2, ".", ",");
    }

    public function getImporteFAttribute()
    {

        return number_format($this->importe, 2, ".", ",");
    }

    public function getFechaHoraRegistroAttribute()
    {
        return ucwords($this->timestamp->formatLocalized('%d %B %Y')) . ' (' . $this->timestamp->format("h:i:s") . ')';
    }

    public function getFechaHoraCierreStrAttribute()
    {
        //dd($this->FechaHoraCierre());
        if ($this->FechaHoraCierre) {
            return ucwords($this->FechaHoraCierre->formatLocalized('%d %B %Y')) . ' (' . $this->FechaHoraCierre->format("h:i:s") . ')';
        }
    }

    public function getFechaHoraAprobacionStrAttribute()
    {
        //dd($this->FechaHoraCierre());
        if ($this->FechaHoraAprobacion) {
            return ucwords($this->FechaHoraAprobacion->formatLocalized('%d %B %Y')) . ' (' . $this->FechaHoraAprobacion->format("h:i:s") . ')';
        }
    }

    public function cancelacion()
    {
        return $this->hasOne(ConciliacionCancelacion::class, 'idconciliacion');
    }

    public function cerrar($id)
    {

        DB::connection('sca')->beginTransaction();

        try {
            if ( ($this->es_historico && ($this->ImportePagado - Conciliacion::HOLGURA_IMPORTE)>$this->importe && ($this->VolumenPagado - Conciliacion::HOLGURA_VOLUMEN)>$this->volumen)) {
                throw new \Exception("No se puede cerrar la conciliación por que el importe y volumen pagados son mayores al importe y volumen conciliados");
            }
            if ( ($this->es_historico && ($this->ImportePagado - Conciliacion::HOLGURA_IMPORTE)>$this->importe && ($this->VolumenPagado - Conciliacion::HOLGURA_VOLUMEN)<=$this->volumen)) {
                throw new \Exception("No se puede cerrar la conciliación por que el importe pagado es mayor al importe conciliado");
            }
            if ( ($this->es_historico && ($this->ImportePagado - Conciliacion::HOLGURA_IMPORTE)<=$this->importe && ($this->VolumenPagado - Conciliacion::HOLGURA_VOLUMEN)>$this->volumen)) {
                throw new \Exception("No se puede cerrar la conciliación por que el volumen pagado es mayor al volumen conciliado");
            }
            if ($this->estado != 0) {
                throw new \Exception("No se puede cerrar la conciliación ya que su estado actual es " . $this->estado_str);
            }

            $repetidos="SELECT count(idviaje_neto) AS CALCULATED_COLUMN1,
                   conciliacion_detalle.idconciliacion_detalle,
                   conciliacion_detalle.idviaje_neto,
                   viajesnetos.Code,
                   group_concat(conciliacion.idconciliacion),
                   group_concat(conciliacion.fecha_conciliacion),
                   viajes.Importe,
                   viajes.IdViaje
              FROM ((conciliacion_detalle conciliacion_detalle
                      INNER JOIN viajesnetos viajesnetos
                         ON     (conciliacion_detalle.idviaje_neto =
                                    viajesnetos.IdViajeNeto)
                            AND (viajesnetos.IdViajeNeto =
                                    conciliacion_detalle.idviaje_neto))
                     INNER JOIN conciliacion conciliacion
                        ON     (conciliacion_detalle.idconciliacion =
                                   conciliacion.idconciliacion)
                           AND (conciliacion.idconciliacion =
                                conciliacion_detalle.idconciliacion))
                   INNER JOIN viajes viajes
                      ON (viajes.IdViajeNeto = viajesnetos.IdViajeNeto)
             WHERE     (conciliacion.fecha_conciliacion >= '2017-07-01 00:00:00')
                   AND conciliacion_detalle.estado = 1
                   AND conciliacion.idconciliacion = '{$id}'
            GROUP BY conciliacion_detalle.idviaje_neto, viajesnetos.Code
            HAVING count(idviaje_neto) > 1";

            //dd($repetidos);

            $r = DB::connection('sca')->select(DB::raw($repetidos));


                //cambiar estatus de un registro
            $this->estado = 1;
            $this->IdCerro = auth()->user()->idusuario;
            $this->FechaHoraCierre = Carbon::now();
            $this->save();

            foreach ($this->viajes() as $v) {
                $viaje = Viaje::find($v->IdViaje);
                if($r!=null){
                    foreach ($r as $item){
                        if($v->code == $item->Code){

                            $detalle = ConciliacionDetalle::find($item->idconciliacion_detalle);
                            $detalle->update([
                                'estado' =>'-1'
                            ]);
                            ConciliacionDetalleCancelacion::create([
                                'idconciliaciondetalle' => $item->idconciliacion_detalle,
                                'motivo' => 'Viaje Duplicado en la conciliacion',
                                'fecha_hora_cancelacion' => Carbon::now(),
                                'idcancelo' => auth()->user()->idusuario
                            ]);
                        }
                    }
                }
                if ($viaje->IdSindicato != $this->idsindicato) {
                    $sindicato_anterior = $viaje->IdSindicato;
                    $viaje->IdSindicato = $this->idsindicato;
                    $viaje->save();

                    DB::connection('sca')->table('cambio_sindicato')->insertGetId([
                        'IdViaje' => $viaje->IdViaje,
                        'IdSindicatoAnterior' => $sindicato_anterior,
                        'IdSindicatoNuevo' => $this->idsindicato,
                        'Registro' => auth()->user()->idusuario
                    ]);
                }
                if ($viaje->IdEmpresa != $this->idempresa) {
                    $empresa_anterior = $viaje->IdEmpresa;
                    $viaje->IdEmpresa = $this->idempresa;
                    $viaje->save();

                    DB::connection('sca')->table('cambio_empresa')->insertGetId([
                        'IdViaje' => $viaje->IdViaje,
                        'IdEmpresaAnterior' => $empresa_anterior,
                        'IdEmpresaNuevo' => $this->idempresa,
                        'Registro' => auth()->user()->idusuario
                    ]);
                }
            }
            DB::connection('sca')->commit();
        } catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }

    public function aprobar()
    {

        DB::connection('sca')->beginTransaction();

        try {
            if ( ($this->es_historico && ($this->ImportePagado - Conciliacion::HOLGURA_IMPORTE)>$this->importe && ($this->VolumenPagado - Conciliacion::HOLGURA_VOLUMEN)>$this->volumen)) {
                throw new \Exception("No se puede aprobar la conciliaciòn por que el importe y volumen pagados son mayores al importe y volumen conciliados");
            }
            if ( ($this->es_historico && ($this->ImportePagado - Conciliacion::HOLGURA_IMPORTE)>$this->importe && ($this->VolumenPagado - Conciliacion::HOLGURA_VOLUMEN)<=$this->volumen)) {
                throw new \Exception("No se puede aprobar la conciliaciòn por que el importe pagado es mayor al importe conciliado");
            }
            if ( ($this->es_historico && ($this->ImportePagado - Conciliacion::HOLGURA_IMPORTE)<=$this->importe && ($this->VolumenPagado - Conciliacion::HOLGURA_VOLUMEN)>$this->volumen)) {
                throw new \Exception("No se puede aprobar la conciliaciòn por que el volumen pagado es mayor al volumen conciliado");
            }
            if ($this->estado != 1) {
                throw new \Exception("No se puede aprobar la conciliación ya que su estado actual es " . $this->estado_str);
            }

            $this->estado = 2;
            $this->IdAprobo = auth()->user()->idusuario;
            $this->FechaHoraAprobacion = Carbon::now();
            $this->save();

            DB::connection('sca')->commit();
        } catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }



    public function revertir_aprovacion(){
        DB::connection('sca')->beginTransaction();

        try {
            $this->estado = 1;
            $this->save();

            $estimacion_conciliacion = EstimacionConciliacion::where('id_conciliacion', '=', $this->idconciliacion)->first();
            if($estimacion_conciliacion) {
                $estimacion_conciliacion->delete();
            }

            DB::connection('sca')->commit();
        } catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }
    public function getEsHistoricoAttribute(){
        if($this->fecha_conciliacion->format("Ymd") <= Conciliacion::FECHA_HISTORICO){
            RETURN TRUE;
        }else{
            RETURN FALSE;
        }
    }
    public function cancelar(Request $request)
    {

        DB::connection('sca')->beginTransaction();

        try {
            if ($this->estado == -1 || $this->estado == -2) {
                throw new \Exception("Ésta conciliación ya ha sido cancelada anteriormente");
            }
            if ($this->estado == 2) {
                throw new \Exception("Ésta conciliación ya ha sido aprobada");
            }

            $estado = $this->estado;
            if($estado == 1){ // validación para visualizar datos en el tablero de control
                $rol = DB::connection("sca")->table("sca_configuracion.role_user")
                    ->where("user_id","=",auth()->user()->idusuario)
                    ->where("role_id", "=", "3")
                    ->where("id_proyecto","=",Context::getId())->count();
            }else{
                $rol = NULL;
            }
            $this->estado = $this->estado == 0 ? -1 : -2;

            ConciliacionCancelacion::create([
                'idconciliacion' => $this->idconciliacion,
                'motivo' => $request->get('motivo'),
                'fecha_hora_cancelacion' => Carbon::now(),
                'idcancelo' => auth()->user()->idusuario,
                'estado_rol_usuario' => $rol
            ]);

            foreach ($this->conciliacionDetalles as $detalle) {

                ConciliacionDetalleCancelacion::create([
                    'idconciliaciondetalle' => $detalle->idconciliacion_detalle,
                    'motivo' => $request->get('motivo'),
                    'fecha_hora_cancelacion' => Carbon::now()->toDateTimeString(),
                    'idcancelo' => auth()->user()->idusuario
                ]);
                $buscar_viaje = DB::connection("sca")->select(DB::raw("select * from viajesnetos where IdViajeNeto = ".$detalle->idviaje_neto.";"));
                /* Bloqueo de cierre de periodo
                           1 : Cierre de periodo
                           0 : Periodo abierto.
                       */
                $cierre = ValidacionCierrePeriodo::validandoCierreViajeDenegar($buscar_viaje[0]->FechaLlegada);

                if($cierre == 1){
                    if($buscar_viaje[0]->denegado == 0) {
                        $save = DB::connection('sca')->table('viajesnetos')->where('IdViajeNeto', '=', $buscar_viaje[0]->IdViajeNeto)->update(['denegado' => 1]);
                    }
                }

                $detalle->estado = -1;
                $detalle->save();
            }
            $this->save();

            DB::connection('sca')->commit();
        } catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }

    public function getEstadoStrAttribute()
    {
        if ($this->estado == 0) {
            return 'Generada';
        } else if ($this->estado == 1) {
            return 'Cerrada';
        } else if ($this->estado == 2) {
            return 'Aprobada';
        } else if ($this->estado < 0) {
            return 'Cancelada';
        }
    }

    public function getFechaInicialAttribute()
    {
        if ($this->viajes()->count()) {
            $result = DB::connection('sca')->select(DB::raw("
                SELECT min(v.FechaLlegada) as fecha_inicial FROM conciliacion c 
                join conciliacion_detalle cd on (c.idconciliacion = cd.idconciliacion and cd.estado = 1)
                join viajes v on (cd.idviaje = v.IdViaje)
                where c.idconciliacion = {$this->idconciliacion} "))[0]->fecha_inicial;
            return Carbon::createFromFormat('Y-m-d', $result)->format('d-m-Y');
        } else {
            return "";
        }
    }

    public function getFechaFinalAttribute()
    {
        if ($this->viajes()->count()) {
            $result = DB::connection('sca')->select(DB::raw("
                SELECT max(v.FechaLlegada) as fecha_final FROM conciliacion c 
                join conciliacion_detalle cd on (c.idconciliacion = cd.idconciliacion and cd.estado = 1)
                join viajes v on (cd.idviaje = v.IdViaje)
                where c.idconciliacion = {$this->idconciliacion} "))[0]->fecha_final;
            return Carbon::createFromFormat('Y-m-d', $result)->format('d-m-Y');
        } else {
            return "";
        }
    }

    public function getRangoAttribute()
    {

        return $this->fecha_inicial != '' && $this->fecha_final != '' ? "DEL (" . $this->fecha_inicial . ") AL (" . $this->fecha_final . ")" : "SIN VIAJES";
    }

    public function getImporteViajesManualesAttribute()
    {
        $results = DB::connection("sca")->select("select sum(Importe) as importe_viajes_manuales "
            . "from conciliacion "
            . "left join conciliacion_detalle on conciliacion.idconciliacion = conciliacion_detalle.idconciliacion "
            . "left join viajes on conciliacion_detalle.idviaje = viajes.IdViaje where conciliacion.idconciliacion = " . $this->idconciliacion . " "
            . "and conciliacion_detalle.estado = 1 "
            . "and viajes.Estatus = 20 "
            . "group by conciliacion.idconciliacion limit 1");
        return $results ? $results[0]->importe_viajes_manuales : 0;
    }

    public function getVolumenViajesManualesAttribute()
    {
        $results = DB::connection("sca")->select("select sum(CubicacionCamion) as Volumen "
            . "from conciliacion "
            . "left join conciliacion_detalle on conciliacion.idconciliacion = conciliacion_detalle.idconciliacion "
            . "left join viajes on conciliacion_detalle.idviaje = viajes.IdViaje where conciliacion.idconciliacion = " . $this->idconciliacion . " "
            . "and conciliacion_detalle.estado = 1 "
            . "and viajes.Estatus = 20 "
            . "group by conciliacion.idconciliacion limit 1");
        return $results ? $results[0]->Volumen : 0;
    }

    public function getVolumenViajesManualesFAttribute()
    {
        return number_format($this->volumen_viajes_manuales, 2, ".", ",");
    }

    public function getImporteViajesManualesFAttribute()
    {
        return number_format($this->importe_viajes_manuales, 2, ".", ",");
    }

    public function getImporteViajesMovilesFAttribute() {
        return number_format(($this->importe - $this->importe_viajes_manuales), 2, ".", ",");
    }

    public function getVolumenViajesMovilesFAttribute() {
        return number_format(($this->volumen - $this->volumen_viajes_manuales), 2, ".", ",");
    }

    public function getPorcentajeImporteViajesManualesAttribute()
    {
        if ($this->importe != 0) {
            return round(($this->importe_viajes_manuales * 100) / $this->importe, 2);
        } else {
            return 0;
        }
    }

    public function getPorcentajeVolumenViajesManualesAttribute()
    {
        if ($this->volumen != 0) {
            return round(($this->volumen_viajes_manuales * 100) / $this->volumen, 2);
        } else {
            return 0;
        }
    }

    public function cambiar_detalles($importe_pagado, $volumen_pagado) {
        DB::connection('sca')->beginTransaction();

        try {
            if ($this->estado != 0 && ($this->ImportePagado>0 || $this->VolumenPagado>0)) {
                throw new \Exception("No se puede cambiar el detalle de la conciliación, su estatus es: " . $this->estado_str);
            }

            $this->ImportePagado = $importe_pagado;
            $this->VolumenPagado = $volumen_pagado;
            $this->save();

            DB::connection('sca')->commit();
        } catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }
}