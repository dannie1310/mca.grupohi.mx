<?php

namespace App\Http\Controllers;

use App\Models\TarifasTipoMaterial;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Tarifas\TarifaMaterial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;

class TarifasMaterialController extends Controller
{
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:desactivar-tarifas-material', ['only' => ['destroy']]);
        $this->middleware('permission:crear-tarifas-material', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-tarifas-material', ['only' => ['edit', 'update']]);

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
            $material = Material::findOrFail($request->get('IdMaterial'));
            return response()->json($material->tarifaMaterial->toArray());
        }
        return view('tarifas.material.index')
                ->withTarifas(TarifaMaterial::all())
                ->withTipos(TarifasTipoMaterial::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateTarifaMaterialRequest $request)
    {
        $request->request->add([
            'Fecha_Hora_Registra' => Carbon::now()->toDateTimeString(),
            'Registra' => auth()->user()->idusuario,
        ]);

        $inicio_vigencia = Carbon::createFromFormat('Y-m-d h:i:s', $request->get('InicioVigencia')." 00:00:00");

        $tarifas = TarifaMaterial::where('IdMaterial', '=', $request->get('IdMaterial'))->where('Estatus', '=', '1')->orderBy("InicioVigencia")->get();

        $conflicto_vigencia = FALSE;
        $i = 0;
        $tarifa_old_sin_vigencia = array();
        $antes_primer_tarifa = FALSE;
        $primer_tarifa = null;
        foreach($tarifas as $tarifa_old) {
            if($i == 0){
                $primer_tarifa = $tarifa_old;
            }
            if($inicio_vigencia->format("Ymd")<$tarifa_old->InicioVigencia){
                $antes_primer_tarifa = TRUE;
                
                break;
            }
            if($tarifa_old->FinVigencia){
                if($inicio_vigencia->format("Ymd")<=$tarifa_old->FinVigencia->format("Ymd") && $inicio_vigencia->format("Ymd")>=$tarifa_old->InicioVigencia->format("Ymd")){
                    $conflicto_vigencia = TRUE;
                }
            }else{
                $tarifa_old_sin_vigencia[] = $tarifa_old;
            }
            $i++;
        }

        
        if($antes_primer_tarifa){

            $fin_vigencia = Carbon::createFromFormat("Y-m-d h:i:s",$primer_tarifa->InicioVigencia->format("Y-m-d")." 00:00:00")->subSeconds(1);
            
            $otros_datos = ['FinVigencia' => $fin_vigencia->format("Y-m-d h:i:s")];
            $datos = array_merge($request->all(),$otros_datos);
            TarifaMaterial::create($datos);

             return redirect()->route('tarifas_material.index');
            
        }else if(!$conflicto_vigencia){
            $fin_vigencia = Carbon::createFromFormat("Y-m-d h:i:s",$request->get('InicioVigencia')." 00:00:00")->subSeconds(1);
            foreach($tarifa_old_sin_vigencia as $tsv){
                $tsv->FinVigencia = $fin_vigencia;
                $tsv->save();
            }

            TarifaMaterial::create($request->all());
             return redirect()->route('tarifas_material.index');
            
        }else{
            Flash::error('Esta tarifa tiene un conflicto de vigencia.');
            return redirect()->route('tarifas_material.index');
        }
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
        $request->request->add([
            'Fecha_Hora_Registra' => Carbon::now()->toDateTimeString(),
            'Registra' => auth()->user()->idusuario,
            'idtarifas_tipo' => $request->idtarifas_tipo
        ]);

        $tarifas = TarifaMaterial::find($id);
        $tarifas->update($request->all());

        Flash::success('¡TARIFA MATERIAL ACTUALIZADO CORRECTAMENTE!');
        return redirect()->route('tarifas_material.index', $tarifas);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
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

    public function create()
    {
        $materiales = Material::orderBy("descripcion")->lists("Descripcion","IdMaterial");
        $tipos = TarifasTipoMaterial::all()->lists("nombre","idtarifas_tipo");
        $fecha_actual = date("d-m-Y");
        return view('tarifas.material.create')->withMateriales($materiales)->withFechaActual($fecha_actual)->withTipos($tipos);
    }

    public function edit($id){
        $tipos = TarifasTipoMaterial::all()->lists("nombre","idtarifas_tipo");
        return view('tarifas.material.edit')
            ->withTarifas(TarifaMaterial::find($id))
            ->withTipos($tipos);
    }
}
