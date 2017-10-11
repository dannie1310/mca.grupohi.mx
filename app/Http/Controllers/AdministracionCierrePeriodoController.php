<?php

namespace App\Http\Controllers;

use App\Models\CierrePeriodo;
use App\Models\Transformers\UsuarioCierresPeriodoTransformers;
use Illuminate\Http\Request;
use App\User;
use App\User_1;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdministracionCierrePeriodoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('context');

        parent::__construct();
    }

    public function init()
    {
        $usuarios=User_1::with('roles')->habilitados()->orderBy('nombre')->orderBy('apaterno')->orderBy('amaterno')->get();
        $cierres= CierrePeriodo::cierres();

        $data = [
            'usuarios'=>UsuarioCierresPeriodoTransformers::transform($usuarios),
            'cierres'=>$cierres
        ];

        return response()->json($data);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('administracion.validacion_cierre_periodo');
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

    public function save(Request $request)
    {
        //
     // dd($request->usuario,$request->cierresSelect);
        $this->validate($request, [
            'usuario' => 'required|string',
            'fecha_inicio' => 'required|date_format:"Y-m-d',
            'fecha_final'=>'required|date_format:"Y-m-d'
        ], [
            'usuario' => 'Debe seleccionar un usuario',
            'fecha_inicio' => 'Debe seleccionar una fecha de inicio valida'. $request->fecha_inicio,
            'fecha_final' => 'Debe seleccionar una fecha final valida'. $request->fecha_final,
        ]);
        foreach ($request->cierresSelect as $cierre) {
            //dd($cierre, $usuario);
            DB::connection('sca')
                ->table('validacion_x_cierre_periodo')
                ->insert(
                    [
                        'idusuario' => $request->usuario,
                        'fecha_inicio' => $request->fecha_inicial,
                        'fecha_fin'=> $request->fecha_final,
                        'usuario_registro' => '3250',
                        'idcierre_periodo' => $cierre
                    ]
                );

        }

        $usuarios=User_1::with('roles')->habilitados()->orderBy('nombre')->orderBy('apaterno')->orderBy('amaterno')->get();
        $cierres= CierrePeriodo::cierres();
        $data = [
            'usuarios'=>UsuarioCierresPeriodoTransformers::transform($usuarios),
            'cierres'=>$cierres
        ];

        return response()->json($data);
    }


}
