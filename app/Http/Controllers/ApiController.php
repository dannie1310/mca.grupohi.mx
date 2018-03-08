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

        $concil = DB::connection('sca')->select('SELECT conciliacion.idconciliacion,viajes.IdMaterial, materiales.Descripcion,conciliacion.idempresa, empresas.RFC, viajes.CubicacionCamion as cubicacion, 
                                                      sum(viajes.Volumen / viajes.CubicacionCamion) as pu, empresas.razonSocial, viajes.IdTiro, tiros_conceptos.id_concepto, sum(Importe) as importe, 
                                                      sum(viajes.volumen) as volumen, sum(viajes.CubicacionCamion) as m_cubicos , format((viajes.Importe / viajes.Volumen), 4) as precio_unitario 
                                                      from conciliacion_detalle 
                                                    join conciliacion using (idconciliacion) 
                                                    join viajes using (idviaje) 
                                                    join materiales using (idmaterial)
                                                    join empresas on (conciliacion.idempresa = empresas.IdEmpresa)
                                                    left join tiros_conceptos on (tiros_conceptos.id_tiro = viajes.IdTiro)
                                                    where conciliacion.idconciliacion = '.$request->id_conciliacion.' and tiros_conceptos.fin_vigencia is null
                                                    group by materiales.IdMaterial, tiros_conceptos.id_concepto, precio_unitario;');

        if(!$concil){
            throw new Exception("No hay datos a Concialiar");
        }

        $partidas_conciliacion = [];
        foreach ($concil as $key => $partida){
            if($partida->id_concepto == null){
                throw new Exception("La Conciliación Contiene Tiros Sin Concepto Asignado");
            }
            if($key == 0){
                $idConciliacion = $partida->idconciliacion;
                $razonSocial = $partida->razonSocial;
                $rfc = $partida->RFC;
                $tipoEmpresa = 1;
                $idCosto = $request->id_costo;
            }
            $partidas_conciliacion[$key]= [
                'tarifa' => $partida->importe / $partida->m_cubicos,
                'material' => $partida->Descripcion,
                'id_material' => $partida->IdMaterial,
                'id_concepto' => $partida->id_concepto,
                'volumen' => $partida->m_cubicos
            ];
        }
        $req = new Request();
        $req->merge([
            'id_conciliacion'   => $idConciliacion,
            'razon_social'      => $razonSocial,
            'rfc'               => $rfc,
            'tipo_empresa'      => $tipoEmpresa,
            'id_costo'          => $idCosto,
            'cumplimiento'      => $request->cumplimiento,
            'vencimiento'       => $request->vencimiento,
            'partidas_conciliacion' => $partidas_conciliacion
        ]);


        return $req->all();

    }

    public function registrarConciliacion(Request $request){
        $est = EstimacionConciliacion::create(['id_estimacion' => $request->id_estimacion, 'id_conciliacion'=>$request->id_conciliacion]);
        return $est->toArray();
    }
}
