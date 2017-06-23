<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Tags\TagModel;
use App\Models\Empresa;
use App\Models\Proyecto;
use DB;
use Auth;
use App\User;

class TagsController extends Controller
{


    public function __construct() {

        $this->middleware('jwt.auth');
    }

    public function lista()
    {
        //$usr = auth()->user();
        //$test = auth()->user()->hasRole([$idusuario]);
        //dd($test);
        // Validación de que el usuario tiene permisos para utilizar el proyecto de regristro de Tags
       $permisos = DB::table('sca_configuracion.permisos_alta_tag')
                    ->whereRaw('(TIMESTAMP(vigencia) > NOW() OR vigencia is null)')
                    ->where('idusuario',Auth::user()->idusuario )->get();
        if(!$permisos){
            return response()->json(['error' => 'No tiene los privilegios para dar de alta tags en los proyectos.', 'code' => 200], 200);
        }

        
        $resp = response()->json(array_merge([
            'proyectos' => Proyecto::select('id_proyecto', 'descripcion')->get()
        ]
        ));

        return $resp;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Almacena los registros de los TAGS enviados desde el dispositivo Móvil
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $datos = $request->json()->all();
        $rol = 19;
        $usr = new User();
        $proy = $usr->rolesApi($rol);

        dd($proy['descripcion']);
        
        // Revisar si existe el UID del Tag
        if(TagModel::where('uid',$datos['uid'])->count() > 0){
            return response()->json(['msj' => 'ok']);
        }else{
            $tag = new TagModel();
            $tag->uid = $datos['uid'];
            $tag->id_proyecto = $datos['id_proyecto'];
            $tag->registro = Auth::user()->idusuario;
            if($tag->save()){
                return response()->json(['msj' => 'true']);
            }else{
                return response()->json(['msj' => 'false']);
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
    public function destroy($id)
    {
        //
    }
}
