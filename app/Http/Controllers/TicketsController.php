<?php

namespace App\Http\Controllers;

use App\Facades\Context;
use App\Models\Proyecto;
use App\Models\ViajeNeto;
use App\Reportes\ViajesNetos;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use DB;
use DateTime;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use PhpSpec\Exception\Exception;

class TicketsController extends Controller
{
    private $deposito_claves = "C:/DKEY/";

    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tickets.index');
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

        $dat = $request->input('data');

        $desc = $this->desencripta($dat);


        $exp = explode("|", $desc);
        if(count($exp)==13) {

            if (Context::getId() == $exp[0]) {
                $resp = DB::connection('sca')->table('sca_configuracion.proyectos')->select('base_datos', 'descripcion')->where('id_proyecto', $exp[0])->first();
                if ($resp == null) {
                    $resp = "No se encontro en la base de datos";
                }

                if ($exp[1] != '0') {
                    $camiont = DB::connection('sca')->table($resp->base_datos . '.camiones')->select('Economico')->where('idCamion', $exp[1])->first();
                    if ($camiont == null) {
                        $camion = "No se encontro en la base de datos";
                    } else {
                        $camion = $camiont->Economico;
                    }
                } else {
                    $camion = "Sin Dato de Camión";
                }
                if ($exp[6] != '0') {
                    $materialt = DB::connection('sca')->table($resp->base_datos . '.materiales')->select('Descripcion')->where('idMaterial', $exp[6])->first();
                    if ($materialt == null) {
                        $material = "No se encontro en la base de datos";
                    } else {
                        $material = $materialt->Descripcion;
                    }
                } else {
                    $material = "Sin Dato de Material.";
                }
                if ($exp[2] != '0') {
                    $origent = DB::connection('sca')->table($resp->base_datos . '.origenes')->select('Descripcion')->where('IdOrigen', $exp[2])->first();

                    if ($origent == null) {
                        $origen = "No se encontro en la base de datos";
                    } else {
                        $origen = $origent->Descripcion;
                    }
                } else {
                    $origen = "Sin Origen.";
                }
                if ($exp[4] != '0') {
                    $tiroA = DB::connection('sca')->table($resp->base_datos . '.tiros')->select('Descripcion')->where('IdTiro', $exp[4])->first();
                    if ($tiroA == null) {
                        $tiro = "No se encontro en la base de datos";
                    } else {
                        $tiro = $tiroA->Descripcion;
                    }
                } else {
                    $tiro = "Sin Tiro";
                }
                //dd($exp[3]);
                if ($exp[3] != '0' || $exp[3] != 'null') {
                    $t = DateTime::createFromFormat("ymdHis", $exp[3]);
                    $fechaSalida = $t->format("d/m/y H:i:s");
                } else {
                    $fechaSalida = "Sin Fecha de Salida.";
                }
                if ($exp[5] != '0' || $exp[3] != 'null') {
                    $d = DateTime::createFromFormat("ymdHis", $exp[5]);
                    $fechaLlegada = $d->format("d/m/y H:i:s");
                } else {
                    $fechaLlegada = "Sin Fecha de Llegada. ";
                }

                $ChInicio = DB::connection('sca')->table($resp->base_datos . '.vw_usuarios_por_proyecto')->select('nombre', 'apaterno', 'amaterno')->where('id_usuario_intranet', $exp[10])->first();
                if ($ChInicio == null) {
                    $ChInicio = "No se encontro en la base de datos";
                }
                $ChCierre = DB::connection('sca')->table($resp->base_datos . '.vw_usuarios_por_proyecto')->select('nombre', 'apaterno', 'amaterno')->where('id_usuario_intranet', $exp[7])->first();
                if ($ChCierre == null) {
                    $ChCierre = "No se encontro en la base de datos";
                }

                $t = DateTime::createFromFormat("ymdHis", $exp[3]);
                $fechaS = $t->format("Y-m-d");
                $horaS = $t->format("H:i:s");

                $d = DateTime::createFromFormat("ymdHis", $exp[5]);
                $fechaL = $d->format("Y-m-d");
                $horaL = $d->format("H:i:s");

               // dd("INSERT INTO prod_sca_pista_aeropuerto_2.viajesnetos(IdArchivoCargado, FechaCarga, HoraCarga, IdProyecto, IdCamion, IdOrigen, FechaSalida, HoraSalida, IdTiro, FechaLlegada, HoraLlegada, IdMaterial, Creo,Code,uidTAG,imei, CreoPrimerToque, CubicacionCamion, IdPerfil) VALUES(0,NOW(),NOW(),".$exp[0].",".$exp[1].",".$exp[2].",'".$fechaS."','".$horaS."',".$exp[4].",'".$fechaL."','".$horaL."',".$exp[6].",".$exp[7].",'".$exp[8].$exp[1]."','".$exp[9]."','".$exp[12]."',".$exp[10].",".$exp[11].",5)");


                return response()->json([
                    'proyecto' => $resp->descripcion,
                    'camion' => $camion,
                    'cubicacion' => $exp[11] . ' m3',
                    'material' => $material,
                    'origen' => $origen,
                    'fechaSalida' => $fechaSalida,
                    'destino' => $tiro,
                    'fechaLlegada' => $fechaLlegada,
                    'ChInicio' => $ChInicio->nombre . ' ' . $ChInicio->apaterno . ' ' . $ChInicio->amaterno,
                    'ChCierre' => $ChCierre->nombre . ' ' . $ChCierre->apaterno . ' ' . $ChCierre->amaterno,
                    'barras' => $exp[8] . $exp[1]
                ], 200);

            }else{
               return response()->json(['error' => 'Esté Ticket no pertenece al proyecto.'],400);
            }
        }else{
            return response()->json(['error' => '¡TICKET INVÁLIDO!'],400);
        }
    }

    function desencripta($txt_encriptado) {

        $texto_desencriptado = 'vacio';

        $texto_encriptado = base64_decode($txt_encriptado);
        //dd($txt_encriptado, $texto_encriptado, "file://" . $this->deposito_claves . "SAO_privada1024.key");
        // $llave_privada = openssl_pkey_get_private("file:\\" . $this->deposito_claves . "privkey.pem", "sao01022013#");
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
        return $texto_desencriptado;
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
