<?php
/**
 * Created by PhpStorm.
 * User: DBENITEZ
 * Date: 14/06/2017
 * Time: 07:31 PM
 */

namespace App\Reportes;

use App\Facades\Context;
use App\Models\Proyecto;
use App\Models\Transformers\InicioViajeReporteTransformer;
use App\User;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;

class InicioViajes
{
    protected $estatus;
    protected $horaInicial;
    protected $horaFinal;
    protected $request;
    protected $data;

    /**
     * InicioViajes constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->horaInicial = Carbon::createFromFormat('g:i:s a', $request->get('HoraInicial'))->toTimeString();
        $this->horaFinal = Carbon::createFromFormat('g:i:s a', $request->get('HoraFinal'))->toTimeString();


        $this->data = InicioViajeReporteTransformer::toArray($request, $this->horaInicial, $this->horaFinal);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excel() {

        if(! $this->data) {
            Flash::error('Ningún inicio de viaje coincide con los datos de consulta');
            return redirect()->back()->withInput();
        }

        return response()->view('reportes.inicio_viajes.table', ['data' => $this->data, 'request' => $this->request])
            ->header('Content-type','text/csv')
            ->header('Content-Disposition' , 'filename=Acarreos Ejecutados por Material '.date("d-m-Y").'_'.date("H.i.s",time()).'.cvs');
    }

    public function show() {

        if(! $this->data) {
            Flash::error('Ningún inicio de viaje  coincide con los datos de consulta');
            return redirect()->back()->withInput();
        }
        return view('reportes.inicio_viajes.show')
            ->withData($this->data)
            ->withRequest($this->request->all());
    }
}