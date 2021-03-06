<?php

namespace App\Http\Controllers;

use App\Models\Conciliacion\Conciliacion;
use App\Models\Conciliacion\ConciliacionDetalle;
use App\Models\Conciliacion\Conciliaciones;
use App\Models\Transformers\ConciliacionDetalleTransformer;
use App\Models\Transformers\ConciliacionTransformer;
use App\Models\ValidacionCierrePeriodo;
use App\Models\Viaje;
use App\Models\ViajeNeto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Conciliacion\ConciliacionDetalleNoConciliado;
use App\Models\Transformers\ConciliacionDetalleNoConciliadoTransformer;
class ConciliacionesDetallesController extends Controller
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
    public function index(Request $request, $id)
    {
        if($request->ajax()) {

            $conciliacion = ConciliacionTransformer::transform(Conciliacion::find($id));

            return response()->json([
                'status_code' => 200,
                'conciliacion' => $conciliacion
            ]);
        }
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
     * @param  \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(Request $request, $id)
    {
        if($request->get('Tipo') == '3') {
            $conciliacion = Conciliacion::find($id);

            $output = (new Conciliaciones($conciliacion))->cargarExcel($request->file('excel'));
            //return response()->json($output);

            Flash::success('<li><strong>VIAJES CONCILIADOS: </strong>' . $output['registros'] . '</li><li>' . '<strong>VIAJES NO CONCILIADOS: </strong>' . $output['registros_nc'] . '</li>');
            return redirect()->back();
        }else if($request->get('Tipo') == '4') {
            if (auth()->user()->hasRole('conciliacion_historico')) {
                $conciliacion = Conciliacion::find($id);
                $fecha_conciliacion = $conciliacion->fecha_conciliacion;
                $fecha_minima = Carbon::createFromFormat('Y-m-d', '2017-04-09');
                if (!($fecha_minima->format("Ymd") >= $fecha_conciliacion->format("Ymd"))) {
                    Flash::error("Esta concilación no puede ser procesada con la opción: Carga Excel Completa");
                    return redirect()->back();
                } else {

                    $output = (new Conciliaciones($conciliacion))->cargarExcelProcesoCompleto($request->file('excel'));
                    Flash::success('<li><strong>VIAJES CONCILIADOS: </strong>' . $output['registros'] . '</li><li>' . '<strong>VIAJES NO CONCILIADOS: </strong>' . $output['registros_nc'] . '</li>');
                    return redirect()->back();
                }
            } else {
                Flash::error('¡LO SENTIMOS, NO CUENTAS CON LOS PERMISOS NECESARIOS PARA REALIZAR LA OPERACIÓN SELECCIONADA!');
                return redirect()->back();
            }
        }

        if($request->ajax()) {
            if ($request->get('Tipo') == '1') {
                try {
                    $conciliacion = Conciliacion::find($id);
                    $output = (new Conciliaciones($conciliacion))->procesaCodigo($request->get('code'));
                    return response()->json($output);

                } catch (\Exception $e) {
                    throw $e;
                }
            } else if ($request->get('Tipo') == '2') {
                
                $conciliacion = Conciliacion::find($id);
                $output = (new Conciliaciones($conciliacion))->procesaArregloIds($request->get('idviaje', []));
                return response()->json($output);

            }
        }
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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param $id_conciliacion
     * @param $id_detalle
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @internal param int $id
     */
    public function destroy(Request $request, $id_conciliacion, $id_detalle)
    {
        DB::connection('sca')->beginTransaction();

        try {
            $conciliacion = Conciliacion::find($id_conciliacion);
            $detalle = ConciliacionDetalle::find($id_detalle);
            if($detalle->estado == -1) {
                throw new \Exception("El viaje ya ha sido cancelado anteriormente");
            }
            if($conciliacion->estado != 0) {
                throw new \Exception("No se puede cancelar el viaje ya que el estado actual de la conciliación es " . $conciliacion->estado_str);
            }

            DB::connection('sca')->table('conciliacion_detalle_cancelacion')->insertGetId([
                'idconciliaciondetalle'  => $id_detalle,
                'motivo'                 => $request->get('motivo'),
                'fecha_hora_cancelacion' => Carbon::now()->toDateTimeString(),
                'idcancelo'              => auth()->user()->idusuario
            ]);

            /*$detalle =  ConciliacionDetalle::find($id_detalle);*/
            $detalle->estado = '-1';
            $detalle->save();
            if($detalle->estado != '-1'){
                 DB::connection('sca')->rollBack();
                throw new \Exception("El detalle de la conciliación no pudo ser cancelado.");
            }

            $buscar_viaje = DB::connection("sca")->select(DB::raw("select * from viajesnetos where IdViajeNeto = ".$detalle->idviaje_neto.";"));
            /* Bloqueo de cierre de periodo
                       1 : Cierre de periodo
                       0 : Periodo abierto.
                   */
            $cierre = ValidacionCierrePeriodo::validandoCierreViajeDenegar($buscar_viaje[0]->FechaLlegada);

            if($cierre == 1){
                if($buscar_viaje[0]->denegado == 0) {
                    $save = DB::connection('sca')->table('viajesnetos')->where('IdViajeNeto', '=', $buscar_viaje[0]->IdViajeNeto)->update(['denegado' => 1]);
                }
            }

            $conciliacion_transformer = ConciliacionTransformer::transform(Conciliacion::find($id_conciliacion));

            DB::connection('sca')->commit();

            return response()->json([
                'status_code' => 200,
                'conciliacion' => $conciliacion_transformer
            ]);
        } catch (\Exception $e) {
            DB::connection('sca')->rollBack();
            throw $e;
        }
    }

    public function detalle_carga($filename) {
        Excel::load(storage_path('exports/excel/') . $filename)->download('xls');
    }
}
