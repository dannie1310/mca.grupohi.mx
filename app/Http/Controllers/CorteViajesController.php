<?php

namespace App\Http\Controllers;

use App\Models\Cortes\Corte;
use App\Models\Cortes\CorteCambio;
use App\Models\Cortes\CorteDetalle;
use App\Models\Cortes\Cortes;
use App\Models\Transformers\ViajeNetoCorteTransformer;
use App\Models\ViajeNeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CorteViajesController extends Controller
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
    public function index($id_corte)
    {
        $viajes_netos = ViajeNeto::scopeCorteEdit($id_corte)->get();
        return response()->json(['viajes_netos' => $viajes_netos]);
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
     * @param  \Illuminate\Http\Request $request
     * @param $id_corte
     * @param $id_viajeneto
     * @return \Illuminate\Http\Response
     * @internal param $id_viaje_neto
     * @internal param int $id
     */
    public function update(Request $request, $id_corte, $id_viajeneto)
    {

        if($request->action == 'revertir_modificaciones') {
            CorteCambio::where('id_viajeneto', $id_viajeneto)->delete();
            DB::connection('sca')
                ->table('corte_detalle')
                ->where('id_corte', $id_corte)
                ->where('id_viajeneto', $id_viajeneto)
                ->update(['estatus' => 1]);


            return response()->json([
                'viaje_neto' => ViajeNeto::scopeCorteEdit($id_corte)->where('viajesnetos.IdViajeNeto', '=', $id_viajeneto)->first(),
            ]);
        }
        if($request->action == 'confirmar') {
            $result = (new Cortes($request->all()))->confirmar_viaje($id_corte, $id_viajeneto);
            return response()->json([
                'viaje_neto' => $result['viaje_neto']
            ]);
        }

        $this->validate($request, [
            'material' => 'required|numeric|exists:sca.materiales,IdMaterial',
            'origen' => 'required|numeric|exists:sca.origenes,IdOrigen',
            'cubicacion' => 'required|numeric',
            'justificacion' => 'required|string'
        ]);

        $result = (new Cortes($request->all()))->modificar_viaje($id_corte, $id_viajeneto);

        if ($request->ajax()) {
            return response()->json([
                'viaje_neto' => $result['viaje_neto']
            ]);
        }
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
