<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Operador;
use App\Models\ProyectoLocal;
use Carbon\Carbon;
use Laracasts\Flash\Flash;

class OperadoresController extends Controller
{
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:desactivar-operadores', ['only' => ['destroy']]);
        $this->middleware('permission:editar-operadores', ['only' => ['edit', 'update']]);
        $this->middleware('permission:crear-operadores', ['only' => ['create', 'store']]);

        parent::__construct();
    }
 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $busqueda = $request->get('buscar');
        return view('operadores.index')
                ->withOperadores(Operador::where(function($query) use ($busqueda) {
                    $query->where('Nombre', 'LIKE', '%'.$busqueda.'%')
                        ->orWhere('NoLicencia', 'LIKE', '%'.$busqueda.'%');
                })->paginate(50))
                ->withBusqueda($busqueda);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('operadores.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateOperadorRequest $request)
    {
        $proyecto_local = ProyectoLocal::where('IdProyectoGlobal', '=', $request->session()->get('id'))->first();
        $request->request->add([
            'IdProyecto' => $proyecto_local->IdProyecto,
            'FechaAlta' => Carbon::now()->toDateString()
        ]);

        $operador = Operador::create($request->all());

        Flash::success('¡OPERADOR REGISTRADO CORRECTAMENTE!');
        return redirect()->route('operadores.show', $operador);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('operadores.show')
                ->withOperador(Operador::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('operadores.edit')
                ->withOperador(Operador::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditOperadorRequest $request, $id)
    {
        $operador = Operador::findOrFail($id);
        $operador->update($request->all());
        
        Flash::success('¡OPERADOR ACTUALIZADO CORRECTAMENTE!');
        return redirect()->route('operadores.show', $operador);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $operador = Operador::findOrFail($id);
        if($operador->Estatus == 1) {
            $operador->Estatus = 0;
            $operador->usuario_desactivo=auth()->user()->idusuario;
            $operador->motivo=$request->motivo;
            $operador->updated_at=date("Y-m-d H:i:s");
            $text = '¡OPERADOR DESHABILITADO CORRECTAMENTE!';
         } else {
            $operador->Estatus = 1;
            $operador->usuario_desactivo=null;
            $operador->usuario_registro=auth()->user()->idusuario;
            $operador->motivo=null;
            $operador->created_at=date("Y-m-d H:i:s");
            $text = '¡OPERADOR DESHABILITADO CORRECTAMENTE!';
        }
        $operador->save();
        Flash::success($text);
        return redirect()->back();
    }
}
