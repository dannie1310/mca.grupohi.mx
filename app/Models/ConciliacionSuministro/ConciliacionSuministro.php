<?php

namespace App\Models\ConciliacionSuministro;

use App\Models\InicioViajes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa;
use App\User;
use App\Models\Sindicato;
use App\Presenters\ModelPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\ConciliacionSuministro\ConciliacionSuministroDetalle;
use Carbon\Carbon;
use PhpParser\Node\Stmt\Return_;
use App\Models\ConciliacionSuministro\ConciliacionDetalleNoConciliado;


class ConciliacionSuministro extends Model
{
    use \Laracasts\Presenter\PresentableTrait;

    const FECHA_HISTORICO = 20170409;
    const HOLGURA_IMPORTE = 50000;
    const HOLGURA_VOLUMEN = 175;

    protected $connection = 'sca';
    protected $table = 'conciliacion_suministro';
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

    public function conciliacionSuministroDetalles()
    {
        return $this->hasMany(ConciliacionSuministroDetalle::class, 'idconciliacion');
    }

    public function conciliacionDetallesNoConciliados()
    {
        return $this->hasMany(ConciliacionSuministroDetalleNoConciliado::class, 'idconciliacion');
    }

    public function sindicato()
    {
        return $this->belongsTo(Sindicato::class, 'idsindicato');
    }

    public function getConciliacionDetallesNoConciliadosPDFAttribute(){
        $detalle_pdf = [];
        foreach($this->conciliacionSuministroDetallesNoConciliados as $detalle){
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
              FROM (inicio_viajes viajes 
                INNER JOIN materiales materiales
                  ON (viajes.IdMaterial = materiales.IdMaterial))
                RIGHT OUTER JOIN conciliacion_suministro_detalle conciliacion_detalle
				  ON (conciliacion_detalle.idviaje = viajes.IdViaje)
			  WHERE (conciliacion_detalle.idconciliacion = ' . $this->idconciliacion . ') 
			  AND(conciliacion_detalle.estado = 1)
			  GROUP BY materiales.Descripcion
			  ORDER BY materiales.Descripcion ASC;'));
    }

    public function materiales_manuales() {
        return DB::connection($this->connection)->select(DB::raw('
            SELECT viajes.IdMaterial, materiales.Descripcion as material 
              FROM (inicio_viajes viajes 
                INNER JOIN materiales materiales
                  ON (viajes.IdMaterial = materiales.IdMaterial))
                RIGHT OUTER JOIN conciliacion_suministro_detalle conciliacion_detalle
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
              FROM (inicio_viajes viajes 
                INNER JOIN materiales materiales
                  ON (viajes.IdMaterial = materiales.IdMaterial))
                RIGHT OUTER JOIN conciliacion_suministro_detalle conciliacion_detalle
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

        foreach ($this->conciliacionSuministroDetalles->where('estado', 1) as $cd) {
            $viajes->push($cd->viaje);
        }
        return $viajes;
    }



    public function viajes_manuales()
    {
        $viajes = new Collection();
        foreach ($this->conciliacionSuministroDetalles->where('estado', 1) as $cd) {
            if($cd->viaje->Estatus == 20) {
                $viajes->push($cd->viaje);
            }
        }
        return $viajes;
    }

    public function viajes_moviles()
    {
        $viajes = new Collection();
        foreach ($this->conciliacionSuministroDetalles->where('estado', 1) as $cd) {
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
              FROM (inicio_viajes viajes 
                INNER JOIN camiones camiones
                  ON (viajes.IdCamion = camiones.IdCamion))
                RIGHT OUTER JOIN conciliacion_suministro_detalle conciliacion_detalle
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
              FROM (inicio_viajes viajes 
                INNER JOIN camiones camiones
                  ON (viajes.IdCamion = camiones.IdCamion))
                RIGHT OUTER JOIN conciliacion_suministro_detalle conciliacion_detalle
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
              FROM (inicio_viajes viajes 
                INNER JOIN camiones camiones
                  ON (viajes.IdCamion = camiones.IdCamion))
                RIGHT OUTER JOIN conciliacion_suministro_detalle conciliacion_detalle
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
        return $this->hasMany(ConciliacionSuministroDetalle::class, 'idconciliacion', 'idconciliacion');
    }

   /* public function getVolumenAttribute()
    {
        $results = DB::connection("sca")->select("select sum(CubicacionCamion) as Volumen "
            . "from conciliacion_suministro "
            . "left join conciliacion_suministro_detalle on conciliacion_suministro.idconciliacion = conciliacion_suministro_detalle.idconciliacion "
            . "left join inicio_viajes on conciliacion_suministro_detalle.idviaje = inicio_viajes.IdInicioViajes where conciliacion_suministro.idconciliacion = " . $this->idconciliacion . " "
            . "and conciliacion_suministro_detalle.estado = 1 "
            . "group by conciliacion_suministro.idconciliacion limit 1");
        return $results ? $results[0]->Volumen : 0;
    }*/

    /*public function getImporteAttribute()
    {
        $results = DB::connection("sca")->select("select sum(Importe) as Importe "
            . "from conciliacion_suministro "
            . "left join conciliacion_suministro_detalle on conciliacion_suministro.idconciliacion = conciliacion_suministro_detalle.idconciliacion "
            . "left join inicio_viajes on conciliacion_suministro_detalle.idviaje = inicio_viajes.IdViaje where conciliacion_suministro.idconciliacion = " . $this->idconciliacion . " "
            . "and conciliacion_suministro_detalle.estado = 1 "
            . "group by conciliacion_suministro.idconciliacion limit 1");
        return $results ? $results[0]->Importe : 0;
    }*/

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
        return $this->hasOne(ConciliacionSuministroCancelacion::class, 'idconciliacion');
    }

    public function cerrar($id)
    {
        DB::connection('sca')->beginTransaction();

        try {

            if ($this->estado != 0) {
                throw new \Exception("No se puede cerrar la conciliación ya que su estado actual es " . $this->estado_str);
            }

            $repetidos="SELECT count(idinicioviaje) AS CALCULATED_COLUMN1,
                   conciliacion_suministro_detalle.idconciliacion_detalle,
                   conciliacion_suministro_detalle.idinicioviaje,
                   inicio_camion.Code,
                   group_concat(conciliacion_suministro.idconciliacion),
                   group_concat(conciliacion_suministro.fecha_conciliacion),
                   inicio_viajes.Importe,
                   inicio_viajes.IdInicioViaje
              FROM ((conciliacion_suministro_detalle conciliacion_detalle
                      INNER JOIN inicio_camion viajesnetos
                         ON     (conciliacion_detalle.idviaje_neto =
                                    viajesnetos.IdViajeNeto)
                            AND (viajesnetos.IdViajeNeto =
                                    conciliacion_detalle.idviaje_neto))
                     INNER JOIN conciliacion_suministro conciliacion
                        ON     (conciliacion_detalle.idconciliacion =
                                   conciliacion.idconciliacion)
                           AND (conciliacion.idconciliacion =
                                conciliacion_detalle.idconciliacion))
                   INNER JOIN inicio_viajes viajes
                      ON (viajes.IdViajeNeto = viajesnetos.IdViajeNeto)
             WHERE     (conciliacion.fecha_conciliacion >= '2017-07-01 00:00:00')
                   AND conciliacion_detalle.estado = 1
                   AND conciliacion.idconciliacion = '{$id}'
            GROUP BY conciliacion_detalle.idinicioviaje, viajesnetos.Code
            HAVING count(idinicioviaje) > 1";

            $r = DB::connection('sca')->select(DB::raw($repetidos));


            //cambiar estatus de un registro
            $this->estado = 1;
            $this->IdCerro = auth()->user()->idusuario;
            $this->FechaHoraCierre = Carbon::now();
            $this->save();

            foreach ($this->viajes() as $v) {
                $viaje = InicioViajes::find($v->IdInicioViaje);
                if($r!=null){
                    foreach ($r as $item){
                        if($v->code == $item->Code){

                            $detalle = ConciliacionSuministroDetalle::find($item->idconciliacion_detalle);
                            $detalle->update([
                                'estado' =>'-1'
                            ]);
                            ConciliacionSuministroDetalleCancelacion::create([
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
    public function getEsHistoricoAttribute(){
        if($this->fecha_conciliacion->format("Ymd") <= ConciliacionSuministro::FECHA_HISTORICO){
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

            $this->estado = $this->estado == 0 ? -1 : -2;

            ConciliacionSuministroCancelacion::create([
                'idconciliacion' => $this->idconciliacion,
                'motivo' => $request->get('motivo'),
                'fecha_hora_cancelacion' => Carbon::now(),
                'idcancelo' => auth()->user()->idusuario
            ]);

            foreach ($this->conciliacionSuministroDetalles as $detalle) {

                ConciliacionSuministroDetalleCancelacion::create([
                    'idconciliaciondetalle' => $detalle->idconciliacion_detalle,
                    'motivo' => $request->get('motivo'),
                    'fecha_hora_cancelacion' => Carbon::now()->toDateTimeString(),
                    'idcancelo' => auth()->user()->idusuario
                ]);

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
                SELECT min(v.Fecha) as fecha_inicial FROM conciliacion_suministro c 
                join conciliacion_suministro_detalle cd on (c.idconciliacion = cd.idconciliacion and cd.estado = 1)
                join inicio_viajes v on (cd.idviaje = v.IdInicioViajes)
                where c.idconciliacion = {$this->idconciliacion} "))[0]->fecha_inicial;
            return $result;
        } else {
            return "";
        }
    }

    public function getFechaFinalAttribute()
    {
        if ($this->viajes()->count()) {
            $result = DB::connection('sca')->select(DB::raw("
                SELECT max(v.Fecha) as fecha_final FROM conciliacion_suministro c 
                join conciliacion_suministro_detalle cd on (c.idconciliacion = cd.idconciliacion and cd.estado = 1)
                join inicio_viajes v on (cd.idviaje = v.IdInicioViajes)
                where c.idconciliacion = {$this->idconciliacion} "))[0]->fecha_final;
            return $result;
        } else {
            return "";
        }
    }

    public function getRangoAttribute()
    {

        return $this->fecha_inicial != '' && $this->fecha_final != '' ? "DEL (" . $this->fecha_inicial . ") AL (" . $this->fecha_final . ")" : "SIN VIAJES";
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
            if ($this->estado != 0 && ($this->VolumenPagado>0)) {
                throw new \Exception("No se puede cambiar el detalle de la conciliación, su estatus es: " . $this->estado_str);
            }

            $this->VolumenPagado = $volumen_pagado;
            $this->save();

            DB::connection('sca')->commit();
        } catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }
}
