<?php
/**
 * Created by PhpStorm.
 * User: DBENITEZ
 * Date: 16/06/2017
 * Time: 06:29 PM
 */

namespace App\Reportes;
use App\Facades\Context;
use App\Models\Proyecto;
use App\Models\Transformers\ConciliacionesDetalladoReporteTransformer;
use App\User;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;


class ConciliacionesDetallado
{

    protected $estatus;
    protected $fechaInicial;
    protected $fechaFinal;
    protected $horaInicial;
    protected $horaFinal;
    protected $hInicial;
    protected $hFinal;
    protected $request;
    protected $data;
    protected $tipo_busqueda;
    protected $codigo;


    /**
     * InicioViajes constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        $this->request = $request;
        $this->tipo_busqueda = $request->get('tipo_busqueda');
        $this->horaInicial = $request->get('HoraInicial');
        $this->horaFinal = $request->get('HoraFinal');
        $this->fechaInicial = Carbon::createFromFormat('Y-m-d', $request->get('FechaInicial'))->toDateString();
        $this->fechaFinal = Carbon::createFromFormat('Y-m-d', $request->get('FechaFinal'))->toDateString();

        if($this->tipo_busqueda == "fecha"){
            $this->codigo = " ";
            $this->hInicial = Carbon::createFromFormat('g:i:s a', $request->get('HoraInicial'))->toTimeString();
            $this->hFinal = Carbon::createFromFormat('g:i:s a', $request->get('HoraFinal'))->toTimeString();
        }else{
            $this->codigo = $request->get('Codigo');
        }

        $request->replace([
            'FechaInicial' => $this->fechaInicial,
            'FechaFinal' => $this->fechaFinal,
            'HoraInicial' => $this->horaInicial,
            'HoraFinal' => $this->horaFinal,
            'Codigo' => $this->codigo]);

        $this->data = ConciliacionesDetalladoReporteTransformer::toArray($request, $this->hInicial, $this->hFinal, $this->codigo);

    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excel() {

        if(! $this->data) {
            Flash::error('Ningún inicio de viaje coincide con los datos de consulta');
            return redirect()->back()->withInput();
        }

        return response()->view('reportes.conciliacion_detalle.table', ['data' => $this->data, 'request' => $this->request])
            ->header('Content-type','text/csv')
            ->header('Content-Disposition' , 'filename=Acarreos Ejecutados por Material '.date("d-m-Y").'_'.date("H.i.s",time()).'.cvs');
    }

    public function show() {

        if(! $this->data) {
            Flash::error('Ningún inicio de viaje  coincide con los datos de consulta');
            return redirect()->back()->withInput();
        }
        return view('reportes.conciliacion_detalle.show')
            ->withData($this->data)
            ->withRequest($this->request->all());
    }
}