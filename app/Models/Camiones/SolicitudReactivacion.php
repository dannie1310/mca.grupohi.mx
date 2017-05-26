<?php

namespace App\Models\Camiones;
use App\Models\Camion;
use App\Models\Empresa;
use App\Models\ImagenCamion;
use App\Models\Marca;
use App\Models\Operador;

use App\Models\Sindicato;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\BinaryOp\Identical;
use Illuminate\Support\Facades\DB;

class SolicitudReactivacion extends Model
{
    protected $connection = 'sca';
    protected $table = 'solicitud_reactivacion_camion';
    protected $primaryKey = 'IdSolicitudReactivacion';
    public $timestamps = false;

    public function getEstatusStringAttribute(){
        switch ($this->Estatus){
            case 0:
                return 'Pendiente';
                break;
            case 1:
                return 'Reactivada';
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
        return $this->hasMany(SolicitudReactivacionImagenes::class,'IdSolicitudReactivacion', 'IdSolicitudReactivacion');
    }

    public function reactivar(){
        DB::connection('sca')->beginTransaction();
        try {
            $statement ="call sca_Solicitud_Reactivacion_camion("
                .$this->IdSolicitudReactivacion.","
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
            $statement ="call sca_Solicitud_Reactivacion_camion("
                .$this->IdSolicitudReactivacion.","
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

