<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Redirect;

class EmpresasController extends Controller
{
    
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:desactivar-empresas', ['only' => ['destroy']]);
        $this->middleware('permission:editar-empresas', ['only' => ['edit', 'update']]);
        $this->middleware('permission:crear-empresas', ['only' => ['create', 'store']]);

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
            return response()->json(Empresa::all());
        }
        
        return view('empresas.index')
                ->withEmpresas(Empresa::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\CreateEmpresaRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateEmpresaRequest $request)
    {
        if(!preg_match('/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/', $request->RFC))
        {
            Flash::error('El rfc tiene formato incorrecto.');
            return Redirect::back()
                ->withInput($request->input());

        }else{
            $empresa = Empresa::create($request->all());
            Flash::success('¡EMPRESA REGISTRADA CORRECTAMENTE');
            return redirect()->route('empresas.show', $empresa);
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
        return view('empresas.show')
                ->withEmpresa(Empresa::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('empresas.edit')
                ->withEmpresa(Empresa::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Requests\EditEmpresaRequest|Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditEmpresaRequest $request, $id)
    {
        if(!preg_match('/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/', $request->RFC))
        {
            Flash::error('El rfc tiene formato incorrecto.');
            return Redirect::back()
                ->withInput($request->input());

        }else{
        $empresa = Empresa::findOrFail($id);
        $empresa->update($request->all());
        
        Flash::success('¡EMPRESA ACTUALIZADA CORRECTAMENTE!');
        return redirect()->route('empresas.show', $empresa);}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $empresa = Empresa::findOrFail($id);
        if($empresa->Estatus == 1) {
            $empresa->Estatus = 0;
            $empresa->usuario_desactivo=auth()->user()->idusuario;
            $empresa->motivo=$request->motivo;
            $empresa->updated_at=date("Y-m-d H:i:s");
            $text = '¡EMPRESA DESHABILITADA CORRECTAMENTE!';
        } else {
            $empresa->Estatus = 1;
            $empresa->motivo=null;
            $empresa->usuario_desactivo=null;
            $empresa->usuario_registro=auth()->user()->idusuario;
            $empresa->created_at=date("Y-m-d H:i:s");
            $text = '¡EMPRESA DESHABILITADA CORRECTAMENTE!';
        }
        $empresa->save();
        Flash::success($text);
        return redirect()->back();
    }
}
