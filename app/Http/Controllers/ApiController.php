<?php

namespace App\Http\Controllers;

use App\Models\Conciliacion\EstimacionConciliacion;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ApiController extends Controller
{

    /**
     * ApiController constructor.
     */
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');

        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function conciliar(Request $request){
        $tarifas_nulas = DB::connection('sca')->select('select  count(1) as cantidad
                        from conciliacion_detalle 
                        join conciliacion using (idconciliacion) 
                        join viajes using (idviaje) 
                        join tarifas using(IdTarifa)
                        where conciliacion.idconciliacion = '.$request->id_conciliacion.'
                        and tarifas.FinVigencia is null and tarifas.idtarifas_tipo is null;');

        if($tarifas_nulas[0]->cantidad > 0)
            throw new Exception("la concilliación $request->id_conciliacion contiene materiales sin tipo de tarifa asignada.");


        $concil = DB::connection('sca')->select('select conciliacion_detalle.idconciliacion, viajes.IdSindicato, viajes.IdEmpresa,  empresas.razonSocial, empresas.RFC, origenes.Descripcion as origen,
                materiales.Descripcion as material, tiros.Descripcion as destino, tarifas.idtarifas_tipo, tarifas_tipo_material.nombre as nombre_tarifa, tiros_conceptos.id_concepto,
                (viajes.Importe / viajes.CubicacionCamion) as pu ,   sum(viajes.CubicacionCamion) as m_cubicos , sum(viajes.volumen) as volumen , sum(Importe) as importe, viajes.IdMaterial
                from conciliacion_detalle
                join viajes using (idviaje)
                join origenes using (IdOrigen)
                join materiales using (idmaterial)
                join tiros using( IdTiro)
                join tarifas using (IdTarifa)
                join tarifas_tipo_material on (tarifas.idtarifas_tipo = tarifas_tipo_material.idtarifas_tipo)
                join empresas on (viajes.IdEmpresa  = empresas.IdEmpresa)
                left join tiros_conceptos on (tiros_conceptos.id_tiro = viajes.IdTiro)
                where idconciliacion = '.$request->id_conciliacion.' and conciliacion_detalle.estado = 1 and tiros_conceptos.fin_vigencia is null
                group by tiros_conceptos.id_concepto, pu, tiros.IdTiro;');

        if(!$concil){
            throw new Exception("No hay datos a concialiar");
        }



        $partidas_conciliacion = [];
        $idTipoTarifa = 0;
        foreach ($concil as $key => $partida){
            if($partida->id_concepto == null){
                throw new Exception("La conciliación Contiene tiros sin concepto asignado");
            }
            if($key == 0){
                $idConciliacion = $partida->idconciliacion;
                $razonSocial = $partida->razonSocial;
                $rfc = $partida->RFC;
                $idTipoTarifa = $partida->idtarifas_tipo;
                $idsindicato = $partida->IdSindicato;
                $id_empresa = $partida->IdEmpresa;
                $tarifa_nombre = $partida->nombre_tarifa;

            }else{
                if($idTipoTarifa != $partida->idtarifas_tipo)
                    throw new Exception("La concilliación $request->id_conciliacion contiene mas de dos diferentes tipos de tarifa asignadas.");
            }

            $partidas_conciliacion[$key]= [
                'tarifa' => $partida->importe / $partida->m_cubicos,
                'material' => $partida->material . ' - '.  $partida->destino,
                'id_material' => $partida->IdMaterial,
                'id_concepto' => $partida->id_concepto,
                'volumen' => $partida->m_cubicos
            ];
        }
        $req = new Request();
        $req->merge([
            'id_conciliacion'   => $idConciliacion,
            'id_empresa'        =>  $id_empresa,
            'razon_social'      => $razonSocial,
            'rfc'               => $rfc,
            'tipo_empresa'      => 1,
            'id_costo'          => $request->id_costo,
            'cumplimiento'      => $request->cumplimiento,
            'vencimiento'       => $request->vencimiento,
            'id_sindicato'      => $idsindicato,
            'sindicato'         => $request->sindicato,
            'tipo_tarifa'       => $idTipoTarifa,
            'nombre_tarifa'     => $tarifa_nombre,
            'partidas_conciliacion' => $partidas_conciliacion
        ]);

        return $req->all();

    }

    public function registrarConciliacion(Request $request){
        $est = EstimacionConciliacion::create(['id_estimacion' => $request->id_estimacion, 'id_conciliacion'=>$request->id_conciliacion]);
        return $est->toArray();
    }
}
