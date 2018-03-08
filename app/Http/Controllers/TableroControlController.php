<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TableroControlController extends Controller
{
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:tablero-control', ['only' => ['index', 'show']]);

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fecha = date('Y-m-d');
        $inicioFecha = strtotime('-7 day', strtotime($fecha));
        $inicioFecha = date('Y-m-d', $inicioFecha);
        //dd($inicioFecha);

        // Viajes no validados y no conciliados.
        $novalidados = DB::connection("sca")->table("viajesnetos as v")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereBetween("v.FechaLlegada",[$inicioFecha,$fecha])
            ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")->count();

        $novalidados_total = DB::connection("sca")->table("viajesnetos as v")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereRaw("v.FechaLlegada < '".$inicioFecha."'")
            ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")->count();

        // Viajes validados y no conciliados.
        $validados = DB::connection("sca")->table("viajesnetos as v")->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
            ->whereBetween("v.FechaLlegada",[$inicioFecha,$fecha])->whereIn("v.Estatus",array('1','21'))
            ->whereNotNull("vr.IdViaje")
            ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")->count();

        $validados_total = DB::connection("sca")->table("viajesnetos as v")->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
            ->whereRaw("v.FechaLlegada < '".$inicioFecha."'")
            ->whereIn("v.Estatus",array('1','21'))
            ->whereNotNull("vr.IdViaje")
            ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")->count();

        return view('tablero-control.index')
                ->withNoValidados($novalidados)
                ->withNoValidadosTotal($novalidados_total)
                ->withValidados($validados)
                ->withValidadosTotal($validados_total);
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
    public function show(Request $request, $id)
    {
        $fecha = date('Y-m-d');
        $inicioFecha = strtotime('-7 day', strtotime($fecha));
        $inicioFecha = date('Y-m-d', $inicioFecha);
        $busqueda = $request->get('buscar');
        if($id == 1){ //no validados y no conciliados
            $novalidados = DB::connection("sca")->table("viajesnetos as v")
                ->selectRaw("v.IdCamion, c.Economico AS economico, v.idorigen, o.Descripcion AS origen, v.FechaSalida AS fs, v.HoraSalida AS hs,
                    v.CubicacionCamion AS cubicacion, v.IdTiro, t.Descripcion AS tiro, v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                    m.Descripcion AS material, v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, IF(v.FechaLlegada >= '".$inicioFecha."','0','1') AS alerta")
                ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->join("camiones as c", "c.IdCamion", "=", "v.IdCamion")
                ->join("origenes as o", "o.IdOrigen","=","v.IdOrigen")
                ->join("tiros as t","t.IdTiro", "=", "v.IdTiro")
                ->join("materiales as m","m.IdMaterial","=", "v.IdMaterial")
                ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")
                ->orderBy("v.FechaLlegada","desc");

            return view('tablero-control.detalle_no_validado')->withTipo(1)->withFechaF($fecha)->withDatos($novalidados->paginate(100))->withBusqueda($busqueda);

        }else if ($id == 2){
            $validados = DB::connection("sca")->table("viajesnetos as v")
                ->selectRaw("v.IdCamion, c.Economico AS economico, v.idorigen, o.Descripcion AS origen, v.FechaSalida AS fs, v.HoraSalida AS hs,
                    v.CubicacionCamion AS cubicacion, v.IdTiro, t.Descripcion AS tiro, v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                    m.Descripcion AS material, v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, IF(v.FechaLlegada >= '".$inicioFecha."','0','1') AS alerta")
                ->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
                ->join("camiones as c", "c.IdCamion", "=", "v.IdCamion")
                ->join("origenes as o", "o.IdOrigen","=","v.IdOrigen")
                ->join("tiros as t","t.IdTiro", "=", "v.IdTiro")
                ->join("materiales as m","m.IdMaterial","=", "v.IdMaterial")
                ->whereIn("v.Estatus",array('1','21'))
                ->whereNotNull("vr.IdViaje")
                ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")
                ->orderBy("v.FechaLlegada","desc");
            return view('tablero-control.detalle_no_validado')->withTipo(2)->withFechaF($fecha)->withDatos($validados->paginate(100))->withBusqueda($busqueda);
        }

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
