<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Models\Camion;
use App\Models\Sindicato;
use App\Models\Empresa;
use App\Contracts\Context;

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
                                            ->join('sindicatos', 'camiones.IdSindicato', '=', 'sindicatos.IdSindicato')
                                            ->join('empresas', 'camiones.IdEmpresa', '=', 'empresas.IdEmpresa')
                                            ->join('marcas', 'camiones.IdMarca', '=', 'marcas.IdMarca')
                                            ->join('operadores', 'camiones.IdOperador', '=', 'operadores.IdOperador')->get(),
                    'sindicatos' => Sindicato::select('IdSindicato as sindicato', 'Descripcion as id')->get(),
                    'empresas' => Empresa::select('razonSocial as empresa', 'IdEmpresa as id')->get()

                ]
                ));
                return $resp;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($role)
    {
        ////$datos = $request->json()->all();
        $rol = 19;
        $usr = new User();
        $proy = $usr->rolesApi($role);

        //dd($cam->select()->get());

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
}
