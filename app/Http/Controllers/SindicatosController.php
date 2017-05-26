<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\Models\Sindicato;
use Illuminate\Support\Facades\Redirect;
class SindicatosController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('context');

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return response()->json(Sindicato::all());
        }
        return view('sindicatos.index')
            ->withSindicatos(Sindicato::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sindicatos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\CreateSindicatoRequest $request)
    {
        if(!preg_match('/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/', $request->rfc))
        {
            Flash::error('El rfc tiene formato incorrecto.');
            return Redirect::back()
                ->withInput($request->input());

        }else{
            $sindicato = Sindicato::create($request->all());
            Flash::success('¡SINDICATO REGISTRADO CORRECTAMENTE');
            return redirect()->route('sindicatos.show', $sindicato);
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('sindicatos.show')
            ->withSindicato(Sindicato::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('sindicatos.edit')
            ->withSindicato(Sindicato::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditSindicatoRequest $request, $id)
    {
        if(!preg_match('/^([A-ZÑ\x26]{3,4}([0-9]{2})(0[1-9]|1[0-2])(0[1-9]|1[0-9]|2[0-9]|3[0-1]))((-)?([A-Z\d]{3}))?$/', $request->rfc))
        {
            Flash::error('El rfc tiene formato incorrecto.');
            return Redirect::back()
                ->withInput($request->input());

        }else{
        $sindicato = Sindicato::findOrFail($id);
        $sindicato->update($request->all());

        Flash::success('¡SINDICATO ACTUALIZADO CORRECTAMENTE!');
        return redirect()->route('sindicatos.show', $sindicato);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $sindicato = Sindicato::findOrFail($id);
        if ($sindicato->Estatus == 1) {
            $sindicato->Estatus = 0;
            $sindicato->usuario_desactivo = auth()->user()->idusuario;
            $sindicato->motivo = $request->motivo;
            $sindicato->updated_at = date("Y-m-d H:i:s");
            $text = '¡SINDICATO DESHABILITADO CORRECTAMENTE!';
        } else {
            $sindicato->Estatus = 1;
            $sindicato->usuario_desactivo = auth()->user()->idusuario;
            $sindicato->motivo = "";
            $sindicato->created_at = date("Y-m-d H:i:s");
            $text = '¡SINDICATO HABILITADO CORRECTAMENTE!';
        }
        $sindicato->save();
        Flash::success($text);
        return redirect()->back();
    }
}
