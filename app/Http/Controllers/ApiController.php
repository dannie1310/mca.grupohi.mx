<?php

namespace App\Http\Controllers;

use App\Models\Conciliacion\ConciliacionDetalle;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;
use PhpSpec\Exception\Example\ErrorException;

class ApiController extends Controller
{
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

        $concil = DB::connection('sca')->select('select conciliacion.idconciliacion,viajes.IdMaterial, materiales.Descripcion,conciliacion.idempresa, empresas.RFC, 
                                                        empresas.razonSocial, viajes.IdTiro, tiros_conceptos.id_concepto, sum(Importe) as importe, sum(viajes.volumen) as volumen, (viajes.Importe / viajes.Volumen) as precio_unitario 
                                                        from conciliacion_detalle 
                              join conciliacion using (idconciliacion) 
                              join viajes using (idviaje) 
                              join materiales using (idmaterial)
                              join empresas on (conciliacion.idempresa = empresas.IdEmpresa)
                              left join tiros_conceptos on (tiros_conceptos.id_tiro = viajes.IdTiro)
                              where conciliacion.idconciliacion = '.$request->header('id-conciliacion').' and tiros_conceptos.fin_vigencia is null
                              group by materiales.IdMaterial, precio_unitario, viajes.IdTiro;');



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
                $idCosto = $request->header('id-costo');
            }
            $partidas_conciliacion[$key]= [
                'tarifa' => $partida->precio_unitario * 50,
                'material' => $partida->Descripcion,
                'id_material' => $partida->IdMaterial,
                'id_concepto' => $partida->id_concepto,
                'volumen' => $partida->volumen
            ];
        }
        $req = new Request();
        $req->merge([
            'id_conciliacion' => $idConciliacion,
            'razon_social' => $razonSocial,
            'rfc' => $rfc,
            'tipo_empresa' => $tipoEmpresa,
            'id_costo' => $idCosto,
            'partidas_conciliacion' => $partidas_conciliacion
        ]);


        //dd($req->all());
        return $req->all();

    }

    public function enviarConciliacion(array $dat){
        $url = 'http://localhost:8003/api/conciliacion';
        $data = $dat;

// use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/form-data\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ dd('no Pandita'); }

        var_dump($result);
    }
    function httpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        dd( $response);
    }
}
