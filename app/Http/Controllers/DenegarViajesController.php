<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DenegarViajesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // DENEGAR VIAJES PARA CIERRES DE PERIODOS

        $datos=[];
        //viajes cancelados en conciliaciones
        $viajes_conciliados_cancelados = DB::connection("sca")->select(DB::raw("select * from viajesnetos vn 
                                        left join conciliacion_detalle cd on vn.IdViajeNeto = cd.idviaje_neto
                                        left join conciliacion c on c.idconciliacion = cd.idconciliacion
                                        where year(vn.FechaLlegada) = 2018 and month(vn.FechaLlegada) = 01
                                        and cd.estado < 0;"));

        $select = "select * from  `prod_sca_pista_aeropuerto_2`.`viajesnetos`  WHERE `IdViajeNeto` in (";
        $archivo=fopen('C:\Users\DBenitezc\Desktop\select.txt',"a") or
        die("No se pudo crear el archivo");
        fputs($archivo,$select);
        fclose($archivo);

        foreach ($viajes_conciliados_cancelados as $viaje){
            $confirmar = DB::connection("sca")->select(DB::raw("select c.idconciliacion, c.estado as estado_conci, cd.idviaje, cd.idviaje_neto, cd.estado as estado_viaje from conciliacion c 
                left join conciliacion_detalle cd on c.idconciliacion = cd.idconciliacion 
                where cd.idviaje_neto = ".$viaje->IdViajeNeto." and cd.estado > 0" ));
            if ($confirmar == []) {
                /*$datos []= [
                   'id' => $viaje->IdViajeNeto
                ];*/
                $save = "UPDATE `prod_sca_pista_aeropuerto_2`.`viajesnetos` SET  `denegado` = 1 WHERE `IdViajeNeto` =".$viaje->IdViajeNeto.";";
                $select = $viaje->IdViajeNeto.", ";

                $archivo=fopen('C:\Users\DBenitezc\Desktop\update.txt',"a") or
                die("No se pudo crear el archivo");
                fputs($archivo,$save);
                fputs($archivo,"\n");
                fclose($archivo);

                $archivo=fopen('C:\Users\DBenitezc\Desktop\select.txt',"a") or
                die("No se pudo crear el archivo");
                fputs($archivo,$select);
                fclose($archivo);

                //$save = DB::connection('sca')->table('viajesnetos')->where('IdViajeNeto', '=',$viaje->IdViajeNeto)->update(['denegado' => 1]);
            }
        }

        //viajes sin conciliaciones
        $viajes_sin_conciliaciones = DB::connection("sca")->select(DB::raw("select * from viajesnetos vn 
                                    left join conciliacion_detalle cd on vn.IdViajeNeto = cd.idviaje_neto
                                    left join conciliacion c on c.idconciliacion = cd.idconciliacion
                                    where year(vn.FechaLlegada) = 2018 and month(vn.FechaLlegada) = 01
                                    and  c.idconciliacion is null;"));

        //dd($viajes_sin_conciliaciones);
        $se = "select * from  `prod_sca_pista_aeropuerto_2`.`viajesnetos`  WHERE `IdViajeNeto` in (";
        $archivo=fopen('C:\Users\DBenitezc\Desktop\select_viajes_sin_conciliar.txt',"a") or
        die("No se pudo crear el archivo");
        fputs($archivo,$se);
        fclose($archivo);

        foreach ($viajes_sin_conciliaciones as $v) {
            $update = "UPDATE `prod_sca_pista_aeropuerto_2`.`viajesnetos` SET  `denegado` = 1 WHERE `IdViajeNeto` = ".$v->IdViajeNeto.";";
            $se = $v->IdViajeNeto.", ";
            $archivo=fopen('C:\Users\DBenitezc\Desktop\viajes_sin_conciliar.txt',"a") or
            die("No se pudo crear el archivo");
            fputs($archivo,$update);
            fputs($archivo,"\n");
            fclose($archivo);
            $archivo=fopen('C:\Users\DBenitezc\Desktop\select_viajes_sin_conciliar.txt',"a") or
            die("No se pudo crear el archivo");
            fputs($archivo,$se);
            fclose($archivo);

        }

        dd("FIN");
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
