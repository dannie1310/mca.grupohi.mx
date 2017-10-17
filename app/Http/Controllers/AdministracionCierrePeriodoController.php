<?php

namespace App\Http\Controllers;

use App\Facades\Context;
use App\Models\CierrePeriodo;
use App\Models\Transformers\UsuarioCierresPeriodoTransformers;
use Illuminate\Http\Request;
use App\User;
use App\User_1;
use App\Http\Requests;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;
use Faker\Provider\DateTime;
use MongoDB\BSON\Timestamp;

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

     //dd($request->usuario,$request->cierresSelect, $request->hora_inicial, $request->hora_final);
        $this->validate($request, [
            'usuario' => 'required|string',
            'fecha_inicial' => 'required|date_format:"Y-m-d',
            'hora_inicial' => 'required|date_format:"H:m a',
            'fecha_final'=>'required|date_format:"Y-m-d',
            'hora_final' => 'required|date_format:"H:m a',
            'cierresSelect' => 'required'
        ], [
            'usuario' => 'Debe seleccionar un usuario',
            'fecha_inicial' => 'Debe seleccionar una fecha de inicio valida '. $request->fecha_inicial,
            'fecha_final' => 'Debe seleccionar una fecha final valida '. $request->fecha_final,
            'hora_inicial' =>'Debe seleccionar una hora inicial valida '.$request->horainicial,
            'hora_final'=>'Debe seleccionar una hora final valida '.$request->horafinal,
            'cierresSelect' => 'Debes seleccionar un periodo para asignar'
        ]);

        foreach ($request->cierresSelect as $cierre) {
           // dd($request->fecha_inicial." ".$request->hora_inicial);



            $fechaInicio = Carbon::createFromFormat('Y-m-d G:i a', $request->fecha_inicial." ".$request->hora_inicial);
            $fechaFin = Carbon::createFromFormat('Y-m-d G:i a', $request->fecha_final." ".$request->hora_final);


            DB::connection('sca')
                ->table('validacion_x_cierre_periodo')
                ->insert(
                    [
                        'idusuario' => $request->usuario,
                        'fecha_inicio' => $fechaInicio->format("Y-m-d G:i:s"),
                        'fecha_fin'=> $fechaFin->format("Y-m-d G:i:s"),
                        'usuario_registro' => Auth::user()->idusuario,
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
