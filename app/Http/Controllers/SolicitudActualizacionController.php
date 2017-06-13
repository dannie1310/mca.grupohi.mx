<?php

namespace App\Http\Controllers;

use App\Models\Camion;
use App\Models\Camiones\SolicitudActualizacion;
use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SolicitudActualizacionController extends Controller
{
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
    public function index(Request $request)
    {

        if($request->ajax()){
            if($request->type == 'buscar') {
                return $this->buscar($request);
            }
            else{
                //dd($request->type.'fgh');
                $solicitudes = DB::connection('sca')->table('solicitud_actualizacion_camion as sol')
                    ->select('IdSolicitudActualizacion','sol.Economico', 'sol.Propietario', 'sol.CubicacionReal', 'sol.CubicacionParaPago', 'sol.FechaHoraRegistro', 'o.Nombre','sol.Estatus')
                    ->leftjoin('Operadores as o','o.IdOperador','=','sol.IdOperador')
                    ->orderBy('FechaHoraRegistro', 'DESC')->limit(10)
                    ->get();
                $data = ['solicitudes' => $solicitudes];
                return response()->json($data);
            }
        }
        else{
            if(auth()->user()->can('consulta-solicitud-actualizar')){
                return view('camiones.solicitud-actualizacion.index');}
                else{
                    Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                    return redirect()->back();
             }

        }
    }

    public function buscar(Request $request){
        if($request->estatus != '') {
            $solicitudes = DB::connection('sca')->table('solicitud_actualizacion_camion as sol')
                ->select('IdSolicitudactualizacion','sol.Economico', 'sol.Propietario', 'sol.CubicacionReal', 'sol.CubicacionParaPago', 'sol.FechaHoraRegistro', 'o.Nombre','sol.Estatus')
                ->leftjoin('Operadores as o','o.IdOperador','=','sol.IdOperador'  )
                ->whereBetween('FechaHoraRegistro',[$request->FechaInicial . ' 00:00:00',$request->FechaFinal . ' 23:59:59'])
                ->where('sol.estatus','=',$request->estatus)
                ->orderBy('FechaHoraRegistro', 'DESC')->get();

        }
        else{
            $solicitudes = DB::connection('sca')->table('solicitud_actualizacion_camion as sol')
                ->select('IdSolicitudActualizacion','sol.Economico', 'sol.Propietario', 'sol.CubicacionReal', 'sol.CubicacionParaPago', 'sol.FechaHoraRegistro', 'o.Nombre','sol.Estatus')
                ->leftjoin('Operadores as o','o.IdOperador','=','sol.IdOperador'  )
                ->whereBetween('FechaHoraRegistro',[$request->FechaInicial . ' 00:00:00',$request->FechaFinal . ' 23:59:59'])
                ->orderBy('FechaHoraRegistro', 'DESC')->get();
        }

        $data = ['solicitudes' => $solicitudes];
        return response()->json($data);
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
        $solicitud = SolicitudActualizacion::find($id);
        $camion = Camion::find($solicitud->IdCamion);

        return view('camiones.solicitud-actualizacion.show')
            ->with([
                'solicitud' => $solicitud,
                'camion'    => $camion]);
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
        $solicitud = SolicitudActualizacion::find($id);
        $camion = Camion::find($solicitud->IdCamion);
        if($request->MotivoRechazo == ''){
            //dd($request->MotivoRechazo);
            $reactivar = $solicitud->reactivar();
        }
        else{
            //dd($request->MotivoRechazo);
            $reactivar = $solicitud->cancelar($request);

        }

        $solicitudes = SolicitudActualizacion::leftjoin('operadores','operadores.Idoperador','=','solicitud_actualizacion_camion.idOperador')->orderBy('FechaHoraRegistro', 'DESC')->limit(10)->get();
        return view('camiones.solicitud-actualizacion.index')->with(['items' => $solicitudes]);
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
