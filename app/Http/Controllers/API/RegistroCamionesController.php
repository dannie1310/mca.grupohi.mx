<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Camion;
use App\Models\Sindicato;
use App\Models\Empresa;
use App\Models\Camiones\SolicitudActualizacion;
use App\Contracts\Context;
use DB;

class RegistroCamionesController extends Controller
{

    

    public function __construct() {

        $this->middleware('jwt.auth');
        $this->middleware('api.context',['except' => 'index']);
    }

    public function lista()
    {
        $resp = response()->json(array_merge([
                    'camiones' => Camion::select('camiones.IdCAmion as id_camion',
                                                'camiones.IdSindicato as id_sindicato',
                                                'camiones.IdEmpresa as id_empresa', 
                                                'sindicatos.Descripcion as sindicato',
                                                'empresas.razonSocial AS empresa',
                                                'camiones.Propietario AS propietario',
                                                'operadores.Nombre AS operador',
                                                'operadores.NoLicencia AS numero_licencia',
                                                'operadores.VigenciaLicencia AS vigencia_licencia',
                                                'camiones.Economico AS economico',
                                                'camiones.Placas AS placas_camion',
                                                'camiones.PlacasCaja AS placas_caja',
                                                'marcas.Descripcion AS marca',
                                                'camiones.Modelo AS modelo',
                                                'camiones.Ancho AS ancho',
                                                'camiones.Largo AS largo',
                                                'camiones.Alto AS alto',
                                                'camiones.EspacioDeGato AS espacio_gato',
                                                'camiones.AlturaExtension AS altura_extension',
                                                'camiones.Disminucion AS disminucion',
                                                'camiones.CubicacionReal AS cubicacion_real',
                                                'camiones.CubicacionParaPago AS cubicacion_para_pago',
                                                'camiones.Estatus as estatus'
                                                )
                                            ->join('sindicatos', 'camiones.IdSindicato', '=', 'sindicatos.IdSindicato', 'left outer')
                                            ->join('empresas', 'camiones.IdEmpresa', '=', 'empresas.IdEmpresa', 'left outer')
                                            ->join('marcas', 'camiones.IdMarca', '=', 'marcas.IdMarca', 'left outer')
                                            ->join('operadores', 'camiones.IdOperador', '=', 'operadores.IdOperador', 'left outer')->get(),
                    'sindicatos' => Sindicato::select('IdSindicato as id', 'Descripcion as sindicato')->get(),
                    'empresas' => Empresa::select('razonSocial as empresa', 'IdEmpresa as id')->get(),
                    'tipos_imagen'=> DB::select("select 'f' as id, 'Frente' as descripcion union select 'd','Derecha' union select 'i','Izquierda' union select 'a','Atras'")

                ]
                ));
        if(!$resp){
            return response()->json(['error' => 'No se pudieron recuperar los catalogos.', 'code' => 200], 200);
        }
        
        return $resp;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($role)
    {
        $usr = new User();
        $proy = $usr->rolesApi($role);
        if($proy == null){
            return response()->json(['error' => 'El usuario no tiene los permisos necesarios.', 'code' => 200], 200);
        }

        $resp = response()->json(array_merge([
            'proyectos' => $proy
        ]
        ));
        return $resp;
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
    public function camion_store(Request $request)
    {
        //
        $camion = new SolicitudActualizacion($request->all());
        $camion->save();


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function imagen_store(Request $request)
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
}
