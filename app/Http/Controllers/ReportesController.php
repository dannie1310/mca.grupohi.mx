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
        $this->middleware('permission:consulta-viajes-netos', ['only' => ['viajes_netos_create']]);

        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viajes_netos_create() {
        return view('reportes.viajes_netos.create');
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
            'FechaFinal'   => 'required|date_format:"Y-m-d"',
            'HoraInicial'  => 'required|date_format:"g:i:s a"',
            'HoraFinal'    => 'required|date_format:"g:i:s a"'
        ]);

        if($request->get('action') == 'view') {
            return (new ViajesNetos($request))->show();
        } else if($request->get('action') == 'excel')
        {
            return (new ViajesNetos($request))->excel();
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


        }else{
           $this->validate($request, [
               'Codigo' => 'required'
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
