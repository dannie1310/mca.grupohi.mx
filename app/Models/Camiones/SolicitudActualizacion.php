<?php

namespace App\Models\Camiones;

use App\Models\Camion;
use App\Models\Empresa;
use App\Models\Marca;
use App\Models\Operador;
use App\Models\Sindicato;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SolicitudActualizacion extends Model
{
    protected $connection = 'sca';
    protected $table = 'solicitud_actualizacion_camion';
    protected $primaryKey = 'IdSolicitudActualizacion';
    public $timestamps = false;

    public function getEstatusStringAttribute(){
        switch ($this->Estatus){
            case 0:
                return 'Pendiente';
                break;
            case 1:
                return 'Procesada';
                break;
            case -1:
                return 'Cancelada';
                break;
        }
    }

    public function camion() {
        return $this->belongsTo(Camion::class, 'IdCamion');
    }

    public function sindicato() {
        return $this->belongsTo(Sindicato::class, 'IdSindicato');
    }

    public function empresa() {
        return $this->belongsTo(Empresa::class, 'IdEmpresa');
    }

    public function operador() {
        return $this->belongsTo(Operador::class, 'IdOperador');
    }

    public function marca() {
        return $this->belongsTo(Marca::class, 'IdMarca');
    }

    public function solicitudImagenes(){
        return $this->hasMany(SolicitudActualizacionImagenes::class,'IdSolicitudActualizacion', 'IdSolicitudActualizacion');
    }

    public function reactivar(){
        DB::connection('sca')->beginTransaction();
        try {
            $statement ="call sca_Solicitud_Actualizacion_camion("
                .$this->IdSolicitudActualizacion.","
                .$this->IdCamion.","
                ."''".","
                .auth()->user()->idusuario.
                ",1,@a);";

            DB::connection("sca")->statement($statement);

            DB::connection('sca')->commit();


        } catch (Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }

    }

    public function cancelar($request){
        DB::connection('sca')->beginTransaction();

        try {
            $statement ="call sca_Solicitud_Actualizacion_camion("
                .$this->IdSolicitudActualizacion.","
                .$this->IdCamion.","
                ."'".$request->MotivoRechazo."'".","
                .auth()->user()->idusuario.
                ",-1,@a);";
            DB::connection("sca")->statement($statement);
            DB::connection('sca')->commit();

        } catch (Exception $e) {
            DB::connection('sca')->rollback();
            throw $e;
        }

    }
}
