<?php

namespace App\Http\Controllers;

use App\Reportes\ConciliacionesDetallado;
use App\Reportes\InicioViajes;
use App\Reportes\ViajesNetos;
use Illuminate\Http\Request;


class ReportesController extends Controller
{

    /**
     * ReportesController constructor.
     */
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');

        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viajes_netos_create() {
        return view('reportes.viajes_netos.diario.create');
    }
    public function viajes_netos_completo_create() {
        return view('reportes.viajes_netos.completo.create');
    }
    public function viajes_netos_auditoria_create() {
        return view('reportes.viajes_netos.auditoria.create');
    }
    public function inicio_viajes_create(){
        return view(('reportes.inicio_viajes.create'));
    }
    public function conciliacion_detalle_create(){
        return view(('reportes.conciliacion_detalle.create'));
    }
    /**
     * @param Request $request
     * @return $this|void
     */
    public function viajes_netos_show(Request $request) {


            $this->validate($request, [
                'FechaInicial' => 'required|date_format:"Y-m-d"',
                'HoraInicial'  => 'required|date_format:"g:i:s a"',
                'HoraFinal'    => 'required|date_format:"g:i:s a"'
            ]);

        if($request->get('action') == 'view') {
            return (new ViajesNetos($request,0))->show();
        } else if($request->get('action') == 'excel')
        {
            return (new ViajesNetos($request,0))->excel();
        }
    }

    public function viajes_netos_completo_show(Request $request) {

       $this->validate($request, [
                'FechaInicial' => 'required|date_format:"Y-m-d"',
                'FechaFinal'   => 'required|date_format:"Y-m-d"',
                'HoraInicial'  => 'required|date_format:"g:i:s a"',
                'HoraFinal'    => 'required|date_format:"g:i:s a"'
       ]);



        if($request->get('action') == 'view') {
            return (new ViajesNetos($request,1))->show();
        } else if($request->get('action') == 'excel')
        {
            return (new ViajesNetos($request,1))->excel();
        }
    }

    public function viajes_netos_auditoria_show(Request $request) {

        $this->validate($request, [
            'FechaInicial' => 'required|date_format:"Y-m-d"',
            'FechaFinal'   => 'required|date_format:"Y-m-d"',
            'HoraInicial'  => 'required|date_format:"g:i:s a"',
            'HoraFinal'    => 'required|date_format:"g:i:s a"'
        ]);



        if($request->get('action') == 'view') {
            return (new ViajesNetos($request,2))->show();
        } else if($request->get('action') == 'excel')
        {
            return (new ViajesNetos($request,2))->excel();
        }
    }

    /**
     * @param Request $request
     * @return $this|void
     */
    public function inicio_viajes_show(Request $request) {

        $this->validate($request, [
            'FechaInicial' => 'required|date_format:"Y-m-d"',
            'FechaFinal'   => 'required|date_format:"Y-m-d"',
            'HoraInicial'  => 'required|date_format:"g:i:s a"',
            'HoraFinal'    => 'required|date_format:"g:i:s a"'
        ]);

        if($request->get('action') == 'view') {
            return (new InicioViajes($request))->show();
        } else if($request->get('action') == 'excel')
        {
            return (new InicioViajes($request))->excel();
        }
    }

    /**
     * @param Request $request
     * @return $this|void
     */
    public function conciliacion_detalle_show(Request $request) {



       if($request->get('tipo_busqueda') == 'fecha') {
           $this->validate($request, [
               'FechaInicial' => 'required|date_format:"Y-m-d"',
               'FechaFinal' => 'required|date_format:"Y-m-d"',
               'HoraInicial' => 'required|date_format:"g:i:s a"',
               'HoraFinal' => 'required|date_format:"g:i:s a"',
               'tipo_busqueda' => 'required'
           ]);

       }
       else if($request->get('tipo_busqueda') == 'folio') {
           $this->validate($request, [
               'Codigo' => 'required',
               'tipo_busqueda' => 'required'
           ]);


       }


        if($request->get('action') == 'view') {
            return (new ConciliacionesDetallado($request))->show();
        } else if($request->get('action') == 'excel')
        {
            return (new ConciliacionesDetallado($request))->excel();
        }
    }
}
