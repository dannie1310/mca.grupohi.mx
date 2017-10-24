<?php

namespace App\Models;

use App\Models\Conciliacion\Conciliacion;
use App\Models\Conciliacion\ConciliacionDetalle;
use DaveJamesMiller\Breadcrumbs\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class Viaje extends Model
{
    protected $connection = 'sca';
    protected $table = 'viajes';
    protected $primaryKey = 'IdViaje';
    public $timestamps = false;

    public function conciliacionDetalles() {
        return $this->hasMany(ConciliacionDetalle::class, 'idviaje','IdViaje');
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

    public function scopePorConciliar($query) {
        return $query->leftJoin('conciliacion_detalle', 'viajes.IdViaje', '=', 'conciliacion_detalle.idviaje')
            ->where(function($query){
                $query->whereNull('conciliacion_detalle.idviaje')
                    ->orWhere('conciliacion_detalle.estado', '=', '-1');
            });
    }

    public function scopeConciliados($query) {
        return $query->leftJoin('conciliacion_detalle', 'viajes.IdViaje', '=', 'conciliacion_detalle.idviaje')
            ->where(function($query){
                $query->whereNotNull('conciliacion_detalle.idviaje')
                    ->orWhere('conciliacion_detalle.estado', '!=', '-1');
            });
    }

    public function material() {
        return $this->belongsTo(Material::class, 'IdMaterial');
    }

    public function disponible() {
        foreach ($this->conciliacionDetalles as $conciliacionDetalle) {
            if ($conciliacionDetalle->estado == 1) {
                return false;
            }
        }
        return true;
    }

    public function cambiarCubicacion(Request $request) {

        DB::connection('sca')->beginTransaction();
        try {

            $conciliacion = Conciliacion::find($request->get('id_conciliacion'));
            if($conciliacion->estado != 0) {
                throw  new \Exception("No se puede cambiar la cubicación del viaje debido al estdo de la conciliación (" . $this->conciliacionDetalles->where('estado', 1)->first()->conciliacion->estado_str . ")");
            }

            DB::connection('sca')->table('cambio_cubicacion')->insertGetId([
                'IdViaje'      => $this->IdViaje,
                'IdViajeNeto'      => $this->IdViajeNeto,
                'VolumenViejo' => $this->CubicacionCamion,
                'VolumenNuevo' => $request->get('cubicacion'),
                'FechaRegistro' => Carbon::now()
            ]);

            $this->CubicacionCamion = $request->get('cubicacion');
            $viaje_neto = $this->viajeNeto;
            $viaje_neto->CubicacionCamion = $request->get('cubicacion');
            $viaje_neto->save();
            $this->save();

            DB::connection("sca")->statement("call calcular_Volumen_Importe(".$this->IdViajeNeto.");");
            DB::connection('sca')->commit();

            return true;

        } catch (\Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }

    }

    public function viajeNeto() {
        return $this->belongsTo(ViajeNeto::class, 'IdViajeNeto');
    }
    public function scopeParaRevertir($query) {

        return $query->whereIn('Estatus', [0,10,20]);
    }


    public static function scopeParaRevertirPeriodo($tipo,$inicial, $final, $codigo)
    {
        if($tipo == 0){
            $dato = "and viajes.FechaLlegada between '{$inicial}' and '{$final}'";
        }else if($tipo == 1){
            $dato ="and viajes.code ='{$codigo}'";
        }
        $sql =  DB::connection('sca')->select(DB::raw("SELECT viajes.*, tiros.Descripcion AS Tiro,
                camiones.Economico AS Camion,
                viajes.CubicacionCamion AS Cubicacion,
                origenes.Descripcion AS Origen,
                materiales.Descripcion AS Material,
                viajes.code AS Codigo,
                c.anio as anio,
                c.mes as mes
                from viajes
                left join tiros on viajes.IdTiro = tiros.IdTiro
                left join camiones on viajes.IdCamion = camiones.IdCamion
                left join origenes on viajes.IdOrigen = origenes.IdOrigen
                left join materiales on viajes.IdMaterial = materiales.IdMaterial
                left join cierres_periodo as c on c.mes = DATE_FORMAT(viajes.FechaLlegada, '%m') and DATE_FORMAT(viajes.FechaLlegada, '%Y')  = c.anio
                where viajes.Estatus in (0, 10, 20)".$dato));

        $existe = array();
        $permiso = 0;
        $anio=0;
        $mes=0;
        foreach ($sql as  $s){
            if($s->mes == NULL && $s->anio==NULL){
                $permiso=1;
            }else {
                $permiso = ValidacionCierrePeriodo::cierreUsuario(Auth::user()->idusuario, $s->mes, $s->anio);
            }
            if($permiso== 1){
                $anio=NULL;
                $mes =NULL;
            }else{
                $anio =$s->anio;
                $mes =$s->mes;
            }
            $existe[]=[
                'IdViaje'=>$s->IdViaje,
                'IdViajeNeto'=>$s->IdViajeNeto,
                'IdSindicato'=>$s->IdSindicato,
                'IdEmpresa'=>$s->IdEmpresa,
                'FechaCarga'=>$s->FechaCarga,
                'HoraCarga'=>$s->HoraCarga,
                'Camion'=>$s->Camion,
                'IdMaquinaria'=>$s->IdMaquinaria,
                'HorasEfectivas'=>$s->HorasEfectivas,
                'CubicacionCamion'=>$s->CubicacionCamion,
                'Origen'=>$s->Origen,
                'FechaSalida'=>$s->FechaSalida,
                'HoraSalida'=>$s->HoraSalida,
                'Tiro'=>$s->Tiro,
                'FechaLlegada'=>$s->FechaLlegada,
                'HoraLlegada'=>$s->HoraLlegada,
                'IdMaterial'=>$s->IdMaterial,
                'FactorAbundamiento'=>$s->FactorAbundamiento,
                'IdChecador'=>$s->IdChecador,
                'Creo'=>$s->Creo,
                'TiempoViaje'=>$s->TiempoViaje,
                'IdRuta'=>$s->IdRuta,
                'Distancia'=>$s->Distancia,
                'TPrimerKM'=>$s->TPrimerKM,
                'TKMSubsecuente'=>$s->TKMSubsecuente,
                'VolumenPrimerKM'=>$s->VolumenPrimerKM,
                'VolumenKMSubsecuentes'=>$s->VolumenKMSubsecuentes,
                'Volumen'=>$s->Volumen,
                'ImportePrimerKM'=>$s->ImportePrimerKM,
                'ImporteKMSubsecuentes'=>$s->ImporteKMSubsecuentes,
                'Importe'=>$s->Importe,
                'Observaciones'=>$s->Observaciones,
                'TipoTarifa'=>$s->TipoTarifa,
                'code'=>$s->code,
                'Elimino'=>$s->Elimino,
                'Modifico'=>$s->Modifico,
                'Cubicacion'=>$s->Cubicacion,
                'Material'=>$s->Material,
                'Codigo'=>$s->Codigo,
                'anio'=>$anio,
                'mes'=>$mes
            ];

        }

        return $existe;
    }

    public function revertir() {
        DB::connection('sca')->beginTransaction();

        try {
            if(count($this->conciliacionDetalles->where('estado', 1))) {
                $conciliacion = $this->conciliacionDetalles->where('estado', 1)->first()->conciliacion;
                throw new \Exception('No se puede revertir el viaje ya que se encuentra relacionado en la conciliación ' . $conciliacion->idconciliacion);
            }
//            if(count($this->conciliacionDetalles->where('estado', -1))) {
//               $this->conciliacionDetalles('estado',-1)->delete();
//            }
            $this->Elimino = auth()->user()->idusuario;
            $this->save();
            $this->delete();

            DB::connection('sca')->commit();
        } catch (Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }
    }
    public function getTipoAttribute(){
        if($this->Estatus>=0 && $this->Estatus<=9){
             return 'Móvil';
        }else{
            return 'Manual';
        }
    }
}
