<?php

namespace App\Http\Controllers;

use App\Models\ConciliacionSuministro\ConciliacionesSuministro;
use App\Models\ConciliacionSuministro\ConciliacionSuministro;
use App\Models\ConciliacionSuministro\ConciliacionSuministroDetalle;
use App\Models\Transformers\ConciliacionSuministroTransformer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ConciliacionesSuministroDetallesController extends Controller
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

            $conciliacion = ConciliacionSuministroTransformer::transform(ConciliacionSuministro::find($id));

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        if($request->ajax()) {
            if ($request->get('Tipo') == '1') {
                try {
                    $conciliacion = ConciliacionSuministro::find($id);
                    $output = (new ConciliacionesSuministro($conciliacion))->procesaCodigo($request->get('code'));
                    return response()->json($output);
                } catch (\Exception $e) {
                    throw $e;
                }
            } else if ($request->get('Tipo') == '2') {
                $conciliacion = ConciliacionSuministro::find($id);
                $output = (new ConciliacionesSuministro($conciliacion))->procesaArregloIds($request->get('idviaje', []));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id_conciliacion, $id_detalle)
    {
        DB::connection('sca')->beginTransaction();

        try {
            $conciliacion = ConciliacionSuministro::find($id_conciliacion);
            $detalle = ConciliacionSuministroDetalle::find($id_detalle);
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

            $conciliacion_transformer = ConciliacionSuministroTransformer::transform(ConciliacionSuministro::find($id_conciliacion));

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
