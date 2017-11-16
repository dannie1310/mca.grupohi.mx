<?php

namespace App\Http\Controllers;

use App\Models\InicioViaje;
use App\Models\Transformers\InicioViajeTransformer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class InicioViajesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            if ($request->get('tipo') == 'conciliar') {
                $this->validate($request, [
                    'IdCamion' => 'exists:sca.camiones,IdCamion',
                    'FechaInicial' => 'required|date_format:"Y-m-d"',
                    'FechaFinal' => 'required|date_format:"Y-m-d"',
                ]);

                if ($request->has('IdCamion')) {
                    $viajes = InicioViaje::porConciliar()
                        ->where('IdCamion', '=', $request->get('IdCamion'))
                        ->whereBetween('FechaSalida', [$request->get('FechaInicial'), $request->get('FechaFinal')])
                        ->orderBy('IdCamion', 'ASC')
                        ->orderBy('FechaSalida', 'ASC')
                        ->orderBy('HoraSalida', 'ASC')
                        ->get();
                } else {
                    $viajes = InicioViaje::porConciliar()
                        ->whereBetween('FechaSalida', [$request->get('FechaInicial'), $request->get('FechaFinal')])
                        ->orderBy('IdCamion', 'ASC')
                        ->orderBy('FechaSalida', 'ASC')
                        ->orderBy('HoraSalida', 'ASC')
                        ->get();
                }

                $filter = $viajes->filter(function ($viaje) {
                    return $viaje->disponible();
                });


                $data = InicioViajeTransformer::transform($filter);

            }
            return response()->json([
                'status_code' => 200,
                'data' => $data
            ]);
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
    public function destroy($id)
    {
        //
    }
}
