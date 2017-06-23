<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\ViajeNeto;
use App\Reportes\ViajesNetos;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use DB;
use DateTime;
use App\Contracts\Context;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use PhpSpec\Exception\Exception;

class TicketsController extends Controller
{
    private $config;

    private $deposito_claves = "C:/DKEY/";

    function __construct(Repository $config) {

        $this->config = $config;
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
    public function show(Request $request)
    {
        //zsddcDsmDjUrSDemDJf6uwN0SM2Ml0e1DLlH%2BJCRVRA3oFwCAB7wC8dXwsGFROq9tX%2FXcHKsDa9U%0AMuvMosSLnV4LJn%2BFmj4xLOOO4jnoQUKm7iZOSYN3BouZqddVbL81KDjWP2OeBJi64nH%2BDzi1ou9M%0AVVepdqof4BSMeIFOSJo%3D%0A
        //bim%2Fk7fNQ%2FLjDHr41VqSuvC9czIlVDFVA2Bvtds%2F515LpMrOqk3wmCrtKHjluDddo2Nu1szZfJ5N%0ACUfyOUwq80oWrGNbtn04cOqs87xkH2xm8cwUQfSHYfrzYXBsUlVi6sPQbcozYWlJINyvImcXorHo%0AU0jb0vgCjDOpHbhAi0c6OpanCIXaHyv4HaCYEzSBrC4lQFqP5Hy7owAiZypZZb7HaJHACBXOFZgX%0AuE2nK5uVqDCKgsKzUPkVSXrullsmlfvGG0Wx5p1TM0lma5Mw3dIQb0062SE1jg29LGGOYxaSXI3Q%0Ax%2F7rw3uERGwSX5QH4BMv%2BHzgc4PzeJknB%2BZ7Rg%3D%3D%0A
        //c%2BcM9CtmbkRhAyK%2BYQYxu7sedasmLXY6CMRt%2Fntq2hmBwSshaY3q37DK9TJt2trQYDJmwnJ%2BPHx%2B%0A5L1xlAbEgOev3l0usZ5hbVmdNC%2FUKn%2BMyCUwek%2BPSl7rfj5nRytuyX%2FAkGq1fMKjStN8pU%2FGwxGD%0AuaK69NtyyFtSjndi6Wo%3D%0A
        //

        //dd($resp);
        //$this->config->set('database.connections.sca.database', $resp[0]['base_datos']);

        $dat = $request->input('c');
        $desc = $this->desencripta($dat);
        $exp = explode("|", $desc);

        $resp = DB::connection('sca')->table('proyectos')->select('base_datos', 'descripcion')->where('id_proyecto', $exp[0])->first();
        if($resp == null){
            $resp="No se encontro en la base de datos";
        }

        if($exp[1] != '0') {
            $camiont = DB::connection('sca')->table($resp->base_datos . '.camiones')->select('Economico')->where('idCamion', $exp[1])->first();
                if($camiont == null){
                    $camion = "No se encontro en la base de datos";
                }else {
                    $camion = $camiont->Economico;
                }
        }else{
            $camion = " ";
        }
        if($exp[6] != '0') {
            $materialt = DB::connection('sca')->table($resp->base_datos . '.materiales')->select('Descripcion')->where('idMaterial', $exp[6])->first();
                if($materialt == null){
                    $material = "No se encontro en la base de datos";
                }else {
                    $material = $materialt->Descripcion;
                }
        }
        else{
            $material = " ";
        }
        if($exp[2] != '0'){
            $origent = DB::connection('sca')->table($resp->base_datos.'.origenes')->select('Descripcion')->where('IdOrigen', $exp[2])->first();
            $origen = $origent->Descripcion;
            if($origent == null){
                $origent = "No se encontro en la base de datos";
            }else {
                $origen = $origent->Descripcion;
            }
        }else{
            $origen = " ";
        }
        if($exp[4] != '0'){
            $tiroA = DB::connection('sca')->table($resp->base_datos.'.tiros')->select('Descripcion')->where('IdTiro', $exp[4])->first();
            if($tiroA == null){
                $tiro = "No se encontro en la base de datos";
            }else {
                $tiro = $tiroA->Descripcion;
            }
        }else{
            $tiro = " ";
        }
        //dd($exp[3]);
        if($exp[3] != '0' || $exp[3] != 'null') {
            $t = DateTime::createFromFormat("ymdHis", $exp[3]);
            $fechaSalida = $t->format("d/m/y H:i:s");
        }else{
            $fechaSalida = " ";
        }
        if($exp[5] != '0' || $exp[3] != 'null') {
            $d = DateTime::createFromFormat("ymdHis", $exp[5]);
            $fechaLlegada = $d->format("d/m/y H:i:s");
        }else{
            $fechaLlegada = " ";
        }

        $ChInicio = DB::connection('sca')->table($resp->base_datos . '.vw_usuarios_por_proyecto')->select('nombre', 'apaterno', 'amaterno')->where('id_usuario_intranet', $exp[10])->first();
        if($ChInicio == null){
            $ChInicio = "No se encontro en la base de datos";
        }
        $ChCierre = DB::connection('sca')->table($resp->base_datos . '.vw_usuarios_por_proyecto')->select('nombre', 'apaterno', 'amaterno')->where('id_usuario_intranet', $exp[7])->first();
        if($ChCierre == null){
            $ChCierre = "No se encontro en la base de datos";
        }


        //dd($exp[4]);
        $respT = response()->json(array_merge([
            'proyecto'    => $resp->descripcion,
            'camion'      => $camion,
            'cubicacion'  => $exp[11].' m3',
            'material'    => $material,
            'origen'      => $origen,
            'fechaSalida' => $fechaSalida,
            'destino'     => $tiro,
            'fechaLlegada'=> $fechaLlegada,
            'ChInicio'    => $ChInicio->nombre.' '.$ChInicio->apaterno.' '.$ChInicio->amaterno,
            'ChCierre'    => $ChCierre->nombre.' '.$ChCierre->apaterno.' '.$ChCierre->amaterno,
            'barras'      => $exp[8].$exp[1]
        ]
        ));

        $info = json_decode($respT->content());

        return view('tickets.create')->with('info', $info );

    }

    function desencripta($texto_encriptado) {


        $texto_encriptado = base64_decode($texto_encriptado);
        // $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "privkey.pem", "sao01022013#");
        $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada1024.key", "sao01022013#");
        openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);
        if($texto_desencriptado==""){
            $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada2048.key", "sao01022013#");
            openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);
            if($texto_desencriptado==""){
                $llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada4096.key", "sao01022013#");
                openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);
            }
        }

        /*$llave_privada = openssl_pkey_get_private("file://" . $this->deposito_claves . "SAO_privada4096.key", "sao01022013#");
            openssl_private_decrypt($texto_encriptado, $texto_desencriptado, $llave_privada);*/
        return ($texto_desencriptado);
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
}
