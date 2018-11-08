<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Ruta;
use App\Models\Tarifas\TarifaRutaMaterial;
use App\Models\TipoTarifa;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;

class TarifasRutaMaterialController extends Controller
{

    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:desactivar-tarifa-ruta-material', ['only' => ['destroy']]);
        $this->middleware('permission:crear-tarifa-ruta-material', ['only' => ['create', 'store']]);
      //  $this->middleware('permission:editar-tarifas-material', ['only' => ['edit', 'update']]);

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request->ajax()) {
           /* $ruta = Ruta::findOrFail($request->get('IdRuta'));
            $material = Material::findOrFail($request->get('IdMaterial'));
            return response()->json($material->tarifaMaterial->toArray());*/
           dd("AQUI");
        }
        return view('tarifas.ruta+material.index')
            ->withTarifas(TarifaRutaMaterial::all())
            ->withTipos(TipoTarifa::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $materiales = Material::where("Estatus", "=", "1")->orderBy("descripcion")->lists("Descripcion","IdMaterial");
        $rutas = Ruta::where("Estatus", "=", "1")->get();
        $tipos = TipoTarifa::all()->lists("descripcion","id");
        $fecha_actual = date("d-m-Y");
        return view('tarifas.ruta+material.create')->withMateriales($materiales)->withRutas($rutas)->withFechaActual($fecha_actual)->withTipos($tipos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateTarifaRutaMaterialRequest $request)
    {
        //
        DB::connection('sca')->table('tarifas_ruta_material')->insert([
            'idtipo_tarifa' => $request->idtipo_tarifa,
            'id_ruta' => $request->id_ruta,
            'id_material' => $request->id_material,
            'primer_km' => $request->primer_km,
            'km_subsecuentes' => $request->km_subsecuente,
            'km_adicionales' => $request->km_adicional,
            'registra' => auth()->user()->idusuario,
            'fecha_hora_registro' => Carbon::now()->toDateTimeString(),
            'inicio_vigencia' => $request->get('inicio_vigencia')." 00:00:00"
        ]);
        return redirect()->route('tarifas_ruta_material.index');
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
        dd("A!".$id);
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

    public function destroy(Request $request, $id)
    {

    /*
     * Estatus:
     *      1 : activa - vigente para su uso en cualquier viaje.
     *      2 : cancelada - Se cancela por error no podrá ser usada para ningún viaje.
     *      0 : desactivada - Se puede utilizar esta tarifa para viajes que entran dentro del rango de fechas entre el inicio y el fin.
     */

        $tarifa = TarifaRutaMaterial::find($id);

        if($request->tipo == 'DESACTIVAR') {
            if ($tarifa->estatus == 1) {
                DB::connection("sca")->table('tarifas_ruta_material')
                    ->where('id', $id)
                    ->update(['estatus' => 0,
                                'desactivo' => auth()->user()->idusuario,
                                'motivo_desactivar' => $request->motivo,
                                'fin_vigencia' => Carbon::now()->toDateTimeString()]);
                Flash::success('¡TARIFA DESACTIVADA CORRECTAMENTE!');
            } else {
                Flash::warning('¡LA TARIFA YA HA SIDO DESACTIVADA!');
            }
        }else{
            if ($tarifa->estatus != 2) {
                $tarifa->update([
                    'estatus' => 2,
                    'cancelo' => auth()->user()->idusuario,
                    'motivo_cancelar' => $request->motivo
                ]);

                Flash::success('¡TARIFA CANCELADA CORRECTAMENTE!');
            } else {
                Flash::warning('¡LA TARIFA YA HA SIDO CANCELADA!');
            }
        }
        return redirect()->back();
    }

}
