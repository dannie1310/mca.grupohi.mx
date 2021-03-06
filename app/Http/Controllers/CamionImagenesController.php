<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ImagenCamion;
use Illuminate\Support\Facades\DB;

class CamionImagenesController extends Controller
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
    public function index(Request $request, $id_camion)
    {
        if($request->ajax()){
            $camion = \App\Models\Camion::findOrFail($id_camion);
            $imagenes = $camion->imagenes;
            $data= [];
            if($imagenes->count() != 0) {
                $data['hasImagenes'] = true;
                foreach($imagenes as $imagen) {
                    //$nombre = explode('/', $imagen->Ruta)[count(explode('/', $imagen->Ruta)) - 1];
                    //$size = Storage::disk('uploads')->size($imagen->Ruta);
                    $data['data'][''.$imagen->TipoC.''] = [
                        'type' => $imagen->Tipo,
                        'url' => 'data:'.$imagen->Tipo.';base64,'.$imagen->Imagen,
                        'data' => [
                            'url' => route('camion.imagenes.destroy', [$id_camion, $imagen->TipoC]),
                            'width' => '50px',
                            'key' => $imagen->TipoC,
                        ]
                    ];
                }
            } 
            return response()->json($data);
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
    public function destroy($id_camion, $tipoC)
    {
        $camion = \App\Models\Camion::findOrFail($id_camion);
        //$imagen = $camion->imagenes->where('TipoC', $tipoC)->first();


        $imagen = $camion->imagenes->where('TipoC', $tipoC)->first();
        $imagenes = $camion->imagenes_hist->where('TipoC', $tipoC);


        if($imagen) {

            foreach ($imagenes as $imagen_hist ) {
                $idCamion = $imagen_hist->IdCamion;
                $insert = DB::connection('sca')->table('camiones_imagenes_historicos')->insert([
                    'IdCamion' => $imagen_hist->IdCamion,
                    'TipoC' => $imagen_hist->TipoC,
                    'Imagen' => $imagen_hist->Imagen,
                    'Tipo' => $imagen_hist->Tipo,
                    'Ruta' => $imagen_hist->Ruta,
                    'Estatus' => $imagen_hist->Estatus,
                    'Usuario' => auth()->user()->idusuario
                ]);
            }

            $delete = DB::connection('sca')->table('camiones_imagenes')
                ->where('IdCamion', $idCamion)
                ->where('TipoC', $tipoC)->delete();

        }

        return response()->json(['success' => true]);    
    }
}
