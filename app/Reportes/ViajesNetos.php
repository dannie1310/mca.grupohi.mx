<?php

/**
 * Created by PhpStorm.
 * User: JFEsquivel
 * Date: 27/03/2017
 * Time: 07:20 PM
 */

namespace App\Reportes;

use App\Facades\Context;
use App\Models\Proyecto;
use App\Models\Transformers\ViajeNetoReporteAuditoriaTransformer;
use App\Models\Transformers\ViajeNetoReporteCompletoTransformer;
use App\Models\Transformers\ViajeNetoReporteTransformer;
use App\User;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Maatwebsite\Excel\Facades\Excel;

class ViajesNetos
{
    protected $estatus;
    protected $horaInicial;
    protected $horaFinal;
    protected $request;
    protected $data;
    protected $tipo;

    /**
     * ViajesNetos constructor.
     * @param Request $request
     */
    public function __construct(Request $request, $i)
    {
        $this->request = $request;
        $this->horaInicial = Carbon::createFromFormat('g:i:s a', $request->get('HoraInicial'))->toTimeString();
        $this->horaFinal = Carbon::createFromFormat('g:i:s a', $request->get('HoraFinal'))->toTimeString();
        $this->tipo = $i;

        switch ($request->get('Estatus')) {
            case '0':
                $this->estatus = 'in (0,1,10,11,20,21,30,31)';
                break;
            case '1':
                $this->estatus = 'in (1,11,21,31)';
                break;
            case '2':
                $this->estatus = 'in (0,10,20,30)';
                break;
        }

        if($request->FechaFinal == 0) {//Reporte diario
            $this->data = ViajeNetoReporteTransformer::toArray($request, $this->horaInicial, $this->horaFinal, $this->estatus);
        }elseif($this->tipo==1){ // Reporte Completo
            $this->data = ViajeNetoReporteCompletoTransformer::toArray($request, $this->horaInicial, $this->horaFinal, $this->estatus);
        }elseif ($this->tipo==2){ //Reporte Auditoria
            $this->data = ViajeNetoReporteAuditoriaTransformer::toArray($request, $this->horaInicial, $this->horaFinal, $this->estatus);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function excel() {

        if(! $this->data) {
            Flash::error('Ningún viaje neto coincide con los datos de consulta');
            return redirect()->back()->withInput();
        }

        if($this->request->FechaFinal==0) {
            return response()->view('reportes.viajes_netos.diario.table', ['data' => $this->data, 'request' => $this->request])
                ->header('Content-type', 'text/csv')
                ->header('Content-Disposition', 'filename=ViajesNetos_' . date("d-m-Y") . '_' . date("H.i.s", time()) . ' Diarios.cvs');
        }elseif ($this->tipo==1){
            return response()->view('reportes.viajes_netos.completo.table', ['data' => $this->data, 'request' => $this->request])
                ->header('Content-type', 'text/csv')
                ->header('Content-Disposition', 'filename=ViajesNetos_' . date("d-m-Y") . '_' . date("H.i.s", time()) . ' Completo.cvs');
        }elseif($this->tipo==2) {
            return response()->view('reportes.viajes_netos.auditoria.table', ['data' => $this->data, 'request' => $this->request])
                ->header('Content-type', 'text/csv')
                ->header('Content-Disposition', 'filename=ViajesNetos_' . date("d-m-Y") . '_' . date("H.i.s", time()) . ' Auditoria.cvs');
        }
    }

    public function show() {

        if(! $this->data) {
            Flash::error('Ningún viaje neto coincide con los datos de consulta');
            return redirect()->back()->withInput();
        }

        if($this->request->FechaFinal==0) {
            return view('reportes.viajes_netos.diario.show')
                ->withData($this->data)
                ->withRequest($this->request->all());
        }elseif($this->tipo==1){
            return view('reportes.viajes_netos.completo.show')
                ->withData($this->data)
                ->withRequest($this->request->all());
        }
        elseif ($this->tipo==2){
            return view('reportes.viajes_netos.auditoria.show')
                ->withData($this->data)
                ->withRequest($this->request->all());
        }
    }
}