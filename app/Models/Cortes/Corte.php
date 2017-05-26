<?php

namespace App\Models\Cortes;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class Corte extends Model
{
    protected $connection = 'sca';
    protected $table = 'corte';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'estatus',
        'id_checador',
        'timestamp_inicial',
        'timestamp_final',
        'motivo'
    ];

    protected $dates = ['timestamp'];

    public function checador() {
        return $this->belongsTo(User::class, 'id_checador');
    }

    public function corte_detalles() {
        return $this->hasMany(CorteDetalle::class, 'id_corte');
    }

    public function viajes_manuales_modificados() {
        return DB::connection('sca')->table('viajesnetos')
            ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('origenes', 'viajesnetos.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('tiros', 'viajesnetos.IdTiro', '=', 'tiros.IdTiro')
            ->leftJoin('materiales', 'viajesnetos.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario as user_primer_toque', 'viajesnetos.CreoPrimerToque', '=', 'user_primer_toque.idusuario')
            ->leftJoin('igh.usuario as user_segundo_toque', 'viajesnetos.Creo', '=', 'user_segundo_toque.idusuario')
            ->leftJoin('corte_cambios', 'viajesnetos.IdViajeNeto', '=', 'corte_cambios.id_viajeneto')
            ->leftJoin('origenes as origenes_nuevos', 'corte_cambios.id_origen_nuevo', '=', 'origenes_nuevos.IdOrigen')
            ->leftJoin('tiros as tiros_nuevos', 'corte_cambios.id_tiro_nuevo', '=', 'tiros_nuevos.IdTiro')
            ->leftJoin('materiales as materiales_nuevos', 'corte_cambios.id_material_nuevo', '=', 'materiales_nuevos.IdMaterial')
            ->join('corte_detalle', function ($join) {
                $join->on('viajesnetos.IdViajeNeto', '=', 'corte_detalle.id_viajeneto')
                    ->where('corte_detalle.id_corte', '=', $this->id);
            })
            ->select(
                "viajesnetos.*",
                "camiones.Economico as camion",
                DB::raw("CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) as FechaHoraLlegada"),
                "origenes.Descripcion as origen",
                "tiros.Descripcion as tiro",
                "materiales.Descripcion as material",
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as creo_primer_toque"),
                DB::raw("CONCAT(user_segundo_toque.nombre, ' ', user_segundo_toque.apaterno, ' ', user_segundo_toque.amaterno) as creo_segundo_toque"),
                "origenes_nuevos.Descripcion as origen_nuevo",
                "tiros_nuevos.Descripcion as tiro_nuevo",
                "materiales_nuevos.Descripcion as material_nuevo",
                "corte_cambios.cubicacion_nueva as cubicacion_nueva",
                "corte_cambios.justificacion as justificacion"
            )
            ->whereIn('viajesnetos.Estatus', [20,21,22,29])
            ->whereNotNull('corte_cambios.id')
            ->get();
    }

    public function viajes_manuales_no_modificados() {
        return DB::connection('sca')->table('viajesnetos')
            ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('origenes', 'viajesnetos.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('tiros', 'viajesnetos.IdTiro', '=', 'tiros.IdTiro')
            ->leftJoin('materiales', 'viajesnetos.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario as user_primer_toque', 'viajesnetos.CreoPrimerToque', '=', 'user_primer_toque.idusuario')
            ->leftJoin('igh.usuario as user_segundo_toque', 'viajesnetos.Creo', '=', 'user_segundo_toque.idusuario')
            ->leftJoin('corte_cambios', 'viajesnetos.IdViajeNeto', '=', 'corte_cambios.id_viajeneto')
            ->leftJoin('origenes as origenes_nuevos', 'corte_cambios.id_origen_nuevo', '=', 'origenes_nuevos.IdOrigen')
            ->leftJoin('tiros as tiros_nuevos', 'corte_cambios.id_tiro_nuevo', '=', 'tiros_nuevos.IdTiro')
            ->leftJoin('materiales as materiales_nuevos', 'corte_cambios.id_material_nuevo', '=', 'materiales_nuevos.IdMaterial')
            ->join('corte_detalle', function ($join) {
                $join->on('viajesnetos.IdViajeNeto', '=', 'corte_detalle.id_viajeneto')
                    ->where('corte_detalle.id_corte', '=', $this->id);
            })
            ->select(
                "viajesnetos.*",
                "camiones.Economico as camion",
                DB::raw("CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) as FechaHoraLlegada"),
                "origenes.Descripcion as origen",
                "tiros.Descripcion as tiro",
                "materiales.Descripcion as material",
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as creo_primer_toque"),
                DB::raw("CONCAT(user_segundo_toque.nombre, ' ', user_segundo_toque.apaterno, ' ', user_segundo_toque.amaterno) as creo_segundo_toque"),
                "origenes_nuevos.Descripcion as origen_nuevo",
                "tiros_nuevos.Descripcion as tiro_nuevo",
                "materiales_nuevos.Descripcion as material_nuevo",
                "corte_cambios.cubicacion_nueva as cubicacion_nueva",
                "corte_cambios.justificacion as justificacion"
            )
            ->whereIn('viajesnetos.Estatus', [20,21,22,29])
            ->whereNull('corte_cambios.id')
            ->get();
    }

    public function viajes_moviles_modificados() {
        return DB::connection('sca')->table('viajesnetos')
            ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('origenes', 'viajesnetos.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('tiros', 'viajesnetos.IdTiro', '=', 'tiros.IdTiro')
            ->leftJoin('materiales', 'viajesnetos.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario as user_primer_toque', 'viajesnetos.CreoPrimerToque', '=', 'user_primer_toque.idusuario')
            ->leftJoin('igh.usuario as user_segundo_toque', 'viajesnetos.Creo', '=', 'user_segundo_toque.idusuario')
            ->leftJoin('corte_cambios', 'viajesnetos.IdViajeNeto', '=', 'corte_cambios.id_viajeneto')
            ->leftJoin('origenes as origenes_nuevos', 'corte_cambios.id_origen_nuevo', '=', 'origenes_nuevos.IdOrigen')
            ->leftJoin('tiros as tiros_nuevos', 'corte_cambios.id_tiro_nuevo', '=', 'tiros_nuevos.IdTiro')
            ->leftJoin('materiales as materiales_nuevos', 'corte_cambios.id_material_nuevo', '=', 'materiales_nuevos.IdMaterial')
            ->join('corte_detalle', function ($join) {
                $join->on('viajesnetos.IdViajeNeto', '=', 'corte_detalle.id_viajeneto')
                    ->where('corte_detalle.id_corte', '=', $this->id);
            })
            ->select(
                "viajesnetos.*",
                "camiones.Economico as camion",
                DB::raw("CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) as FechaHoraLlegada"),
                "origenes.Descripcion as origen",
                "tiros.Descripcion as tiro",
                "materiales.Descripcion as material",
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as creo_primer_toque"),
                DB::raw("CONCAT(user_segundo_toque.nombre, ' ', user_segundo_toque.apaterno, ' ', user_segundo_toque.amaterno) as creo_segundo_toque"),
                "origenes_nuevos.Descripcion as origen_nuevo",
                "tiros_nuevos.Descripcion as tiro_nuevo",
                "materiales_nuevos.Descripcion as material_nuevo",
                "corte_cambios.cubicacion_nueva as cubicacion_nueva",
                "corte_cambios.justificacion as justificacion"
            )
            ->whereIn('viajesnetos.Estatus', [0,1])
            ->whereNotNull('corte_cambios.id')
            ->get();
    }

    public function viajes_moviles_no_modificados() {
        return DB::connection('sca')->table('viajesnetos')
            ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('origenes', 'viajesnetos.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('tiros', 'viajesnetos.IdTiro', '=', 'tiros.IdTiro')
            ->leftJoin('materiales', 'viajesnetos.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario as user_primer_toque', 'viajesnetos.CreoPrimerToque', '=', 'user_primer_toque.idusuario')
            ->leftJoin('igh.usuario as user_segundo_toque', 'viajesnetos.Creo', '=', 'user_segundo_toque.idusuario')
            ->leftJoin('corte_cambios', 'viajesnetos.IdViajeNeto', '=', 'corte_cambios.id_viajeneto')
            ->leftJoin('origenes as origenes_nuevos', 'corte_cambios.id_origen_nuevo', '=', 'origenes_nuevos.IdOrigen')
            ->leftJoin('tiros as tiros_nuevos', 'corte_cambios.id_tiro_nuevo', '=', 'tiros_nuevos.IdTiro')
            ->leftJoin('materiales as materiales_nuevos', 'corte_cambios.id_material_nuevo', '=', 'materiales_nuevos.IdMaterial')
            ->join('corte_detalle', function ($join) {
                $join->on('viajesnetos.IdViajeNeto', '=', 'corte_detalle.id_viajeneto')
                    ->where('corte_detalle.id_corte', '=', $this->id);
            })
            ->select(
                "viajesnetos.*",
                "camiones.Economico as camion",
                DB::raw("CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) as FechaHoraLlegada"),
                "origenes.Descripcion as origen",
                "tiros.Descripcion as tiro",
                "materiales.Descripcion as material",
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as creo_primer_toque"),
                DB::raw("CONCAT(user_segundo_toque.nombre, ' ', user_segundo_toque.apaterno, ' ', user_segundo_toque.amaterno) as creo_segundo_toque"),
                "origenes_nuevos.Descripcion as origen_nuevo",
                "tiros_nuevos.Descripcion as tiro_nuevo",
                "materiales_nuevos.Descripcion as material_nuevo",
                "corte_cambios.cubicacion_nueva as cubicacion_nueva",
                "corte_cambios.justificacion as justificacion"
            )
            ->whereIn('viajesnetos.Estatus', [0,1])
            ->whereNull('corte_cambios.id')
            ->where('corte_detalle.estatus', '=', 2)
            ->get();
    }

    public function viajes_moviles_no_confirmados() {
        return DB::connection('sca')->table('viajesnetos')
            ->leftJoin('camiones', 'viajesnetos.IdCamion', '=', 'camiones.IdCamion')
            ->leftJoin('origenes', 'viajesnetos.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('tiros', 'viajesnetos.IdTiro', '=', 'tiros.IdTiro')
            ->leftJoin('materiales', 'viajesnetos.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario as user_primer_toque', 'viajesnetos.CreoPrimerToque', '=', 'user_primer_toque.idusuario')
            ->leftJoin('igh.usuario as user_segundo_toque', 'viajesnetos.Creo', '=', 'user_segundo_toque.idusuario')
            ->leftJoin('corte_cambios', 'viajesnetos.IdViajeNeto', '=', 'corte_cambios.id_viajeneto')
            ->leftJoin('origenes as origenes_nuevos', 'corte_cambios.id_origen_nuevo', '=', 'origenes_nuevos.IdOrigen')
            ->leftJoin('tiros as tiros_nuevos', 'corte_cambios.id_tiro_nuevo', '=', 'tiros_nuevos.IdTiro')
            ->leftJoin('materiales as materiales_nuevos', 'corte_cambios.id_material_nuevo', '=', 'materiales_nuevos.IdMaterial')
            ->join('corte_detalle', function ($join) {
                $join->on('viajesnetos.IdViajeNeto', '=', 'corte_detalle.id_viajeneto')
                    ->where('corte_detalle.id_corte', '=', $this->id);
            })
            ->select(
                "viajesnetos.*",
                "camiones.Economico as camion",
                DB::raw("CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada) as FechaHoraLlegada"),
                "origenes.Descripcion as origen",
                "tiros.Descripcion as tiro",
                "materiales.Descripcion as material",
                DB::raw("CONCAT(user_primer_toque.nombre, ' ', user_primer_toque.apaterno, ' ', user_primer_toque.amaterno) as creo_primer_toque"),
                DB::raw("CONCAT(user_segundo_toque.nombre, ' ', user_segundo_toque.apaterno, ' ', user_segundo_toque.amaterno) as creo_segundo_toque"),
                "origenes_nuevos.Descripcion as origen_nuevo",
                "tiros_nuevos.Descripcion as tiro_nuevo",
                "materiales_nuevos.Descripcion as material_nuevo",
                "corte_cambios.cubicacion_nueva as cubicacion_nueva",
                "corte_cambios.justificacion as justificacion"
            )
            ->whereIn('viajesnetos.Estatus', [0,1])
            ->whereNull('corte_cambios.id')
            ->where('corte_detalle.estatus', '=', 1)
            ->get();
    }

    public function viajes_netos_confirmados()
    {
        $result = new Collection();
        foreach (CorteDetalle::where(['id_corte' => $this->id, 'estatus' => 2])->get() as $detalle) {
            $result->push($detalle->viajeNeto);
        }
        return $result;
    }

    public function viajes_netos_no_confirmados() {
        $result = new Collection();
        foreach (CorteDetalle::where(['id_corte' => $this->id, 'estatus' => 1])->get() as $detalle) {
            $result->push($detalle->viajeNeto);
        }
        return $result;
    }

    public function getFechaAttribute() {
        return $this->timestamp->format('d-M-Y');
    }

    public function scopePorChecador($query) {
        if(auth()->user()->hasRole('checador')) {
            return $query->where('id_checador', auth()->user()->idusuario);
        }
        if(auth()->user()->hasRole('jefe-acarreos')) {
            return $query;
        }
    }

    public function getTimestampAttribute($timestamp) {
        return new Date($timestamp);
    }

    public function getEstadoAttribute() {
        switch ($this->estatus) {
            case 1:
                return 'INICIADO';
                break;
            case 2:
                return 'CERRADO';
                break;
        }
    }
}