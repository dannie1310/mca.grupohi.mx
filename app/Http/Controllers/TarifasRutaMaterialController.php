<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Ruta;
use App\Models\Tarifas\TarifaRutaMaterial;
use App\Models\TipoTarifa;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
        $materiales = Material::orderBy("descripcion")->lists("Descripcion","IdMaterial");
        $rutas = Ruta::all();
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

    public function destroy(Request $request, $id)
    {
        dd($request);
        $tarifa = TarifaMaterial::find($id);
        if($tarifa->Estatus == 1) {
            $tarifa->update([
                'Estatus'  => 0,
                'usuario_desactivo' => auth()->user()->idusuario,
                'motivo'  => $request->motivo
            ]);
            Flash::success('¡TARIFA DESACTIVADA CORRECTAMENTE!');
        } else {
            Flash::warning('¡LA TARIFA YA HA SIDO DESACTIVADA!');
        }
        return redirect()->back();
    }

}
