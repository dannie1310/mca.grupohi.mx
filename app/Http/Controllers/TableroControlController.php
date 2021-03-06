<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Facades\Context;

class TableroControlController extends Controller
{
    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        $this->middleware('permission:tablero-control', ['only' => ['index', 'show']]);

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fecha = date('Y-m-d');
        $inicioFecha = strtotime('-7 day', strtotime($fecha));
        $inicioFecha = date('Y-m-d', $inicioFecha);
        //dd($inicioFecha, );

        // Viajes no validados y no conciliados.
        /*$novalidados = DB::connection("sca")->table("viajesnetos as v")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereBetween("v.FechaLlegada",["'".$inicioFecha."'","'".$fecha."'"])
            ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")->count();


        $novalidados_total = DB::connection("sca")->table("viajesnetos as v")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereNull("vr.IdViajeNeto")->count();



        $novalidados_total = DB::connection("sca")->table("viajesnetos as v")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereRaw("v.FechaLlegada <= '".$inicioFecha."'")
            ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")->count();*/

        $novalidados_total = DB::connection("sca")->table("viajesnetos as vn")
            ->leftjoin("viajes as v","vn.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereNull("v.IdViajeNeto")
            ->whereNull("vr.IdViajeRechazado")->count();


        // Viajes validados y no conciliados.

        $validados_total = DB::connection("sca")->table("viajes as v")
            ->leftjoin(DB::raw('(select idviaje_neto from conciliacion_detalle where estado =1) cd'),
                function($join)
                {
                    $join->on('v.IdViajeNeto', '=', 'cd.idviaje_neto');
                })->whereNull("cd.idviaje_neto")->count();


       /*$validados = DB::connection("sca")->table("viajesnetos as v")->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
             ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
             ->whereBetween("v.FechaLlegada",["'".$inicioFecha."'","'".$fecha."'"])->whereIn("v.Estatus",array('1','21'))
             ->whereNotNull("vr.IdViaje")
             ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")->count();

         $validados_total = DB::connection("sca")->table("viajesnetos as v")->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
             ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
             ->whereRaw("v.FechaLlegada <= '")
             ->whereIn("v.Estatus",array('1','21'))
             ->whereNotNull("vr.IdViaje")
             ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")->count();*/

        //usuario con diferentes imei
        $usuarios_imei = DB::connection("sca")->table("telefonos")
            ->selectRaw("count('id_checador') as total")
            ->where("estatus","=","1")
            ->whereNotNull("id_checador")
            ->groupBy("id_checador")->havingRaw("count('id_checador')>1")->get();
        $usuarios_imei =$this->sumar($usuarios_imei);

        //un IMEI con diferentes usuarios
        $imei_usuario = DB::connection("sca")->table("telefonos")
            ->selectRaw("count('imei') as total")
            ->where("estatus","=","1")
            ->whereNotNull("imei")
            ->groupBy("imei")->havingRaw("count('imei')>1")->get();
        $imei_usuario = $this->sumar($imei_usuario);

        //imei con diferente impresora
        $imei_impresora = DB::connection("sca")->table("telefonos")
            ->selectRaw("count('imei') as total")
            ->where("estatus","=","1")
            ->whereNotNull("id_impresora")
            ->groupBy("imei")->havingRaw("count('imei')>1")->get();

        $imei_impresora = $this->sumar($imei_impresora);

        //impresora con diferente imei
        $impresora_imei = DB::connection("sca")->table("telefonos")
            ->selectRaw("count('id_impresora') as total")
            ->where("estatus","=","1")
            ->whereNotNull("id_impresora")
            ->groupBy("id_impresora")->havingRaw("count('id_impresora')>1")->get();
        $impresora_imei = $this->sumar($impresora_imei);

        //Conciliaciones: cancelacion sin permiso de gerente
        $cancela = DB::connection("sca")->table("conciliacion_cancelacion")
                    ->whereNotNull("estado_rol_usuario")
                    ->where("estado_rol_usuario", "=", "0")->count();

        //Viajes: camiones con mas de un viaje manual
        $camion_manual = DB::connection("sca")->table("viajesnetos")
            ->selectRaw("count('IdCamion') as total")
            ->join("folios_vales_manuales", "Code", "=", "folio")
            ->whereIn("Estatus", array('29','20','21'))
            ->whereNotNull("Code")
            ->whereNotNull("id_viaje_neto")
            ->groupBy("IdCamion")
            ->havingRaw("count(IdCamion)>1")->get();
        $camion_manual = $this->sumar($camion_manual);

        //Conciliaciones: creacion, revision y autorizacion con el mismo usuario.
        $validacion_conciliacion = DB::connection("sca")->table("conciliacion")
            ->whereRaw("IdRegistro=IdCerro")
            ->whereRaw("IdRegistro=IdAprobo")
            ->whereRaw("IdCerro=IdAprobo")->count();

        //Camiones: Cambio de Cubicación. ----- No terminado
        $cubicacion = DB:: connection("sca")->table("camiones as c")
            ->selectRaw("DISTINCT c.idcamion, c.Economico, c.Placas, c.CubicacionParaPago as cubicacionPagoActual, c.CubicacionReal as cubicacionRealActual,
                        h.CubicacionParaPago as cubicacionPago, h.CubicacionReal as cubicacionReal, h.created_at, h.updated_at, h.Estatus")
            ->join("camiones_historicos_solicitudes as h", "c.IdCamion","=","h.IdCamion")
            ->whereRaw("h.CubicacionParaPago != c.CubicacionParaPago")
            ->whereRaw("h.CubicacionReal != c.CubicacionReal")
            ->where("h.CubicacionReal", "!=", "0")
            ->where("h.CubicacionParaPago", "!=", "0")
            ->where("c.CubicacionReal", "!=", "0")
            ->where("c.CubicacionParaPago", "!=", "0")
            ->where("c.Estatus", "=", "1")
            ->where("h.Estatus","=", "1")
            ->orderBy("c.IdCamion")->count();

        //Tarifas: Distintas para un mismo material.
        $tarifas_m = DB::connection("sca")->table("tarifas")
            ->selectRaw("count(*) as total")
            ->groupBy("IdMaterial")
            ->havingRaw("count(*)>1")->get();
        $tarifas_m = $this->sumar($tarifas_m);

        //Camiones: mas de 3 viajes en un Turno.
        $camiones_viajes = DB::connection("sca")->select(DB::raw("select count(*) as suma from viajesnetos where FechaLlegada = '".$fecha."' and HoraLlegada between '07:00:00' and '19:00:00' group by IdCamion having count(IdCamion)>3 order by IdCamion asc"));
        $ayer = strtotime('-1 day', strtotime($fecha));
        $ayer = date('Y-m-d', $ayer);
        $camiones_segundo_turno = DB::connection("sca")->select(DB::raw("SELECT count(*) as suma,IdCamion FROM viajesnetos where (FechaLlegada = '".$ayer."' and HoraLlegada between '19:00:00' and '23:59:59') or (FechaLlegada = '".$fecha."' and HoraLlegada between '00:00:00' and '06:59:59') group by IdCamion having count(IdCamion) > 3 order by IdCamion;"));
        foreach ($camiones_viajes as $c){
            $camiones_viajes = $c->suma;
        }
        foreach ($camiones_segundo_turno as $c){
            $camiones_viajes = $camiones_viajes+ $c->suma;
        }
        if($camiones_viajes == [ ]){
            $camiones_viajes = 0;
        }
        $sum = 0;
        $placas_camion= DB::connection("sca")->select(
            DB::raw("select Economico,Placas, count(Placas) as suma from camiones group by Placas having count(Placas) > 1")
        );
        foreach ($placas_camion as $i) {
            $sum = $sum + 1;
        }

        $data [] = [
            '0' => 'Viajes no validados y no conciliados.',
            '1' => $novalidados_total,
            '2' => 1
                ];
        $data [] = [
            '0' =>'Viajes validados y no conciliados.',
            '1'=> $validados_total,
            '2' => 2
            ];
        $data [] = [
            '0' => 'Usuarios con diferentes IMEI.',
            '1' => $usuarios_imei,
            '2' => 3
            ];
        $data [] = [
            '0' => 'IMEI con diferentes usuarios.',
            '1'=> $imei_usuario,
            '2' => 4
            ];
        $data [] = [
            '0' => 'Impresora con diferentes IMEI.',
            '1' => $impresora_imei,
            '2' => 6
            ];
        $data [] = [
            '0' => 'IMEI con diferentes impresoras.',
            '1' => $imei_impresora,
            '2' => 5
            ];
        $data [] = [
            '0' => 'Conciliaciones canceladas sin perfil de Gerente.',
            '1' => $cancela,
            '2' => 7
            ];
        $data [] = [
            '0' => 'Camiones con más de un viaje manual.',
            '1' => $camion_manual,
            '2' => 8
            ];
        $data [] = [
            '0' => 'Creación, revisión y autorización de conciliaciones con el mismo usuario.',
            '1' => $validacion_conciliacion,
            '2' => 9
            ];
        /*$data [] = [
            '0' => 'Camiones: Cambio de Cubicación.',
            '1' => $cubicacion,
            '2' => 10
            ]; // pendiente de finalizar
        */
        $data [] = [
            '0' => 'Tarifas distintas para un mismo material.',
            '1' => $tarifas_m,
            '2' => 11
            ];
        $data [] = [
            '0' => 'Camiones con más de 3 viajes en un turno.',
            '1' => $camiones_viajes,
            '2' => 12
            ];
        $data [] = [
            '0' => 'Camiones con las mismas placas pero diferente económico.',
            '1' => $sum,
            '2' => 13
            ];
        $datos = $this->ordenar($data);
        return view('tablero-control.index')
            ->withContador(1)
            ->withDatos($datos);
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
    public function show(Request $request, $id)
    {
        //dd($request, $id);
        $fecha = date('Y-m-d');
        $inicioFecha = strtotime('-7 day', strtotime($fecha));
        $inicioFecha = date('Y-m-d', $inicioFecha);
        $ayer = strtotime('-1 day', strtotime($fecha));
        $ayer = date('Y-m-d', $ayer);
        $dosSemanas = strtotime('-7 day', strtotime($inicioFecha));
        $dosSemanas = date('Y-m-d', $dosSemanas);
        $cuatroSemanas = strtotime('-28 day', strtotime($inicioFecha));
        $cuatroSemanas = date('Y-m-d', $cuatroSemanas);
        $tercermes = strtotime('-90 day', strtotime($cuatroSemanas));
        $tercermes = date('Y-m-d', $tercermes);
//dd($inicioFecha, $dosSemanas, $cuatroSemanas, $tercermes);
        $busqueda = $request->get('buscar');
        if($id == 1){ //no validados y no conciliados --- mayores a una semana
            $novalidados = DB::connection("sca")->table("viajesnetos as vn")
                ->selectRaw("vn.IdCamion, vn.idorigen, vn.FechaSalida AS fs, vn.HoraSalida AS hs, vn.CubicacionCamion AS cubicacion, vn.IdTiro, vn.FechaLlegada AS fl, 
                            vn.HoraLlegada AS hl, vn.IdMaterial,
                            vn.Code AS code, vn.folioMina AS foliomina, vn.folioSeguimiento AS folioseg, 
                            IF(vn.FechaLlegada >= '".$dosSemanas."','0','1') AS alerta,
                            IF(vn.estatus = 29, 'Viaje Manual - Pendiente de Autorizar',
                            IF(vn.estatus = 20, 'Viaje Manual - Pendiente de Validar',
                            IF(vn.estatus = 0, 'Viaje - Pendiente por Validar',''))) AS estatus,
                            IF(vn.denegado = 1, 'DENEGADO', '') AS denegado,
                        c.Economico AS economico, o.Descripcion AS origen, t.Descripcion AS tiro, m.Descripcion AS material")
                ->leftjoin("viajes as v","vn.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->join("camiones as c", "c.IdCamion", "=", "vn.IdCamion")
                ->join("origenes as o", "o.IdOrigen","=","vn.IdOrigen")
                ->join("tiros as t","t.IdTiro", "=", "vn.IdTiro")
                ->join("materiales as m","m.IdMaterial","=", "vn.IdMaterial")
                ->whereNull("v.IdViajeNeto")->whereNull("vr.IdViajeRechazado")
                ->whereRaw("vn.FechaLlegada <= '".$inicioFecha."'")
                ->orderBy("vn.FechaLlegada","desc");

            /*$semanas = DB::connection("sca")->select(
                DB::raw("SELECT  count(*) as uno FROM viajesnetos AS vn
              LEFT JOIN viajes AS v ON v.IdViajeNeto = vn.IdViajeNeto
              LEFT JOIN viajesrechazados AS vr ON vr.IdViajeNeto = v.IdViajeNeto
              WHERE  v.IdViajeNeto IS NULL AND vr.IdViajeRechazado IS NULL and vn.FechaLlegada BETWEEN '".$cuatroSemanas."' AND '".$inicioFecha."';"));

            $tresMeses= DB::connection("sca")->select("SELECT  count(*) as uno FROM viajesnetos AS vn
              LEFT JOIN viajes AS v ON v.IdViajeNeto = vn.IdViajeNeto
              LEFT JOIN viajesrechazados AS vr ON vr.IdViajeNeto = v.IdViajeNeto
              WHERE  v.IdViajeNeto IS NULL AND vr.IdViajeRechazado IS NULL and vn.FechaLlegada BETWEEN '".$tercermes."' AND '".$cuatroSemanas."';");

            $cuatroMasMeses= DB::connection("sca")->select("SELECT  count(*) as uno FROM viajesnetos AS vn
              LEFT JOIN viajes AS v ON v.IdViajeNeto = vn.IdViajeNeto
              LEFT JOIN viajesrechazados AS vr ON vr.IdViajeNeto = v.IdViajeNeto
              WHERE  v.IdViajeNeto IS NULL AND vr.IdViajeRechazado IS NULL and vn.FechaLlegada < '".$tercermes."';");*/

            $semanas = DB::connection("sca")->table("viajesnetos as vn")
                ->leftjoin("viajes as v","vn.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->whereNull("v.IdViajeNeto")
                ->whereNull("vr.IdViajeRechazado")
                ->whereRaw("vn.FechaLlegada BETWEEN '".$cuatroSemanas."' and '".$inicioFecha."'")->count();

            $tresMeses = DB::connection("sca")->table("viajesnetos as vn")
                ->leftjoin("viajes as v","vn.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->whereNull("v.IdViajeNeto")
                ->whereNull("vr.IdViajeRechazado")
                ->whereRaw("vn.FechaLlegada BETWEEN '".$tercermes."' and '".$cuatroSemanas."'")->count();

            $cuatroMasMeses = DB::connection("sca")->table("viajesnetos as vn")
                ->leftjoin("viajes as v","vn.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->whereNull("v.IdViajeNeto")
                ->whereNull("vr.IdViajeRechazado")
                ->whereRaw("vn.FechaLlegada < '".$tercermes."'")->count();

            //dd($semanas[0]->uno, $tresMeses[0]->dos, $cuatroMasMeses);
            //dd($novalidados->count(), $semanas, $sem, $tresMeses, $meses3, $cuatroMasMeses, $mas4, $inicioFecha, $cuatroSemanas, $tercermes);
            return view('tablero-control.detalle_no_validado')->withTipo(1)->withFechaF($fecha)->withSemanas($semanas)->withTresMeses($tresMeses)->withMas($cuatroMasMeses)->withDatos($novalidados->paginate(100))->withBusqueda($busqueda);

        }else if ($id == 2){

            $validados = DB::connection("sca")->table("viajes as va")
                ->selectRaw("v.IdCamion,  v.idorigen,  v.FechaSalida AS fs, v.HoraSalida AS hs,
                            v.CubicacionCamion AS cubicacion, v.IdTiro,  v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                            v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, 
                            IF(v.FechaLlegada >= '".$dosSemanas."','0','1') AS alerta,
                            IF(v.estatus = 29, 'Viaje Manual - Cargado',
                            IF(v.estatus = 20, 'Viaje Manual - Pendiente Validar',
                            IF(v.estatus = 0, 'Viaje - Pendiente por Validar',
                            IF(v.estatus = 1, 'Viaje - Validado', 
                            IF(v.estatus = 21, 'Validado',''))))) AS estatus,
                            IF(v.denegado = 1, 'DENEGADO', '') AS denegado,
                            c.Economico AS economico, o.Descripcion AS origen, t.Descripcion AS tiro, m.Descripcion AS material")
                ->leftjoin(DB::raw('(select idviaje_neto from conciliacion_detalle where estado =1) cd'),
                    function($join)
                    {
                        $join->on('va.IdViajeNeto', '=', 'cd.idviaje_neto');
                    })
                ->join("viajesnetos as v", "va.IdViajeNeto", "=", "v.IdViajeNeto")
                ->join("camiones as c", "c.IdCamion", "=", "v.IdCamion")
                ->join("origenes as o", "o.IdOrigen","=","v.IdOrigen")
                ->join("tiros as t","t.IdTiro", "=", "v.IdTiro")
                ->join("materiales as m","m.IdMaterial","=", "v.IdMaterial")
                ->whereNull("cd.idviaje_neto")
                ->orderBy("v.FechaLlegada","desc");




            /*$semanas = DB::connection("sca")->select("SELECT  COUNT(*) AS uno FROM viajesnetos AS v LEFT JOIN viajes AS vr ON vr.IdViajeNeto = v.IdViajeNeto LEFT JOIN conciliacion_detalle AS cd ON cd.idviaje_neto = v.IdViajeNeto
                      WHERE v.Estatus IN (1 , 21) AND vr.IdViaje IS NOT NULL AND v.FechaLlegada BETWEEN '".$cuatroSemanas."' AND '".$inicioFecha."' AND (cd.idconciliacion_detalle IS NULL OR cd.estado = -1);");

            $tresMeses = DB::connection("sca")->select("SELECT  COUNT(*) AS dos FROM viajesnetos AS v LEFT JOIN viajes AS vr ON vr.IdViajeNeto = v.IdViajeNeto LEFT JOIN conciliacion_detalle AS cd ON cd.idviaje_neto = v.IdViajeNeto
                         WHERE v.Estatus IN (1 , 21) AND vr.IdViaje IS NOT NULL AND v.FechaLlegada BETWEEN '".$tercermes."' AND '".$cuatroSemanas."' AND (cd.idconciliacion_detalle IS NULL OR cd.estado = -1); ");


            $Meses3 = DB::connection("sca")->table("viajesnetos as vn")
                ->leftjoin("viajes as v","vn.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->whereNull("v.IdViajeNeto")
                ->whereNull("vr.IdViajeRechazado")
                ->whereRaw("vn.FechaLlegada BETWEEN '".$tercermes."' and '".$cuatroSemanas."'")->count();

            $cuatroMasMeses = DB::connection("sca")->table("viajesnetos as v")->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
                ->whereIn("v.Estatus",array('1','21'))
                ->whereNotNull("vr.IdViaje")
                ->whereRaw("v.FechaLlegada < '".$tercermes."'")
                ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")->count();*/

            $semanas = DB::connection("sca")->table("viajes as v")
                ->leftjoin(DB::raw('(select idviaje_neto from conciliacion_detalle where estado =1) cd'),
                    function($join)
                    {
                        $join->on('v.IdViajeNeto', '=', 'cd.idviaje_neto');
                    })->whereNull("cd.idviaje_neto")
                ->whereRaw("v.FechaLlegada BETWEEN '".$cuatroSemanas."' and '".$inicioFecha."'")
                ->count();


            $tresMeses = DB::connection("sca")->table("viajes as v")
                ->leftjoin(DB::raw('(select idviaje_neto from conciliacion_detalle where estado =1) cd'),
                    function($join)
                    {
                        $join->on('v.IdViajeNeto', '=', 'cd.idviaje_neto');
                    })->whereNull("cd.idviaje_neto")
                ->whereRaw("v.FechaLlegada BETWEEN '".$tercermes."' and '".$cuatroSemanas."'")
                ->count();

            $cuatroMasMeses = DB::connection("sca")->table("viajes as v")
                ->leftjoin(DB::raw('(select idviaje_neto from conciliacion_detalle where estado =1) cd'),
                    function($join)
                    {
                        $join->on('v.IdViajeNeto', '=', 'cd.idviaje_neto');
                    })->whereNull("cd.idviaje_neto")
                ->whereRaw("v.FechaLlegada < '".$tercermes."'")
                ->count();



            return view('tablero-control.detalle_no_validado')->withTipo(2)->withFechaF($fecha)->withSemanas($semanas)->withTresMeses($tresMeses)->withMas($cuatroMasMeses)->withDatos($validados->paginate(100))->withBusqueda($busqueda);
        }else if($id == 3){
            //usuario con diferentes imei
            $usuario = DB::connection("sca")->table("telefonos")->selectRaw("id_checador")
                    ->where("estatus","=","1")
                    ->whereNotNull("id_checador")
                    ->groupBy("id_checador")->havingRaw("count('id_checador')>1")->get();

            foreach ($usuario as $u) {
                $datos = DB::connection("sca")->table("telefonos as t")
                    ->selectRaw("t.id as id, t.imei as imei, t.id_checador,u.nombre, u.apaterno, u.amaterno, t.marca, t.modelo, t.linea, t.id_impresora")
                    ->join("igh.usuario as u", "u.idusuario", "=","t.id_checador")
                    ->where("t.estatus", "=", "1")
                    ->where("t.id_checador", "=",$u->id_checador)->get();
                foreach ($datos as $r) {
                    $dat[] = [
                        'id' => $r->id,
                        'imei' => $r->imei,
                        'usuario' => $r->id_checador,
                        'nombre' => $r->nombre." ".$r->apaterno." ".$r->amaterno,
                        'marca' => $r->marca,
                        'modelo' => $r->modelo,
                        'linea' => $r->linea,
                        'impresora' => $r->id_impresora,
                        'mac' => ""
                     ];
                }
            }
             return view('tablero-control.telefonos_detalle')->withTipo(3)->withFechaF($fecha)->withTelefono($dat);
        }else if($id == 4){
            $imei = DB::connection("sca")->table("telefonos")->where("estatus","=","1")
                ->whereNotNull("imei")
                ->groupBy("imei")->havingRaw("count('imei')>1")->get();
            foreach ($imei as $u) {
                $datos = DB::connection("sca")->table("telefonos as t")
                    ->selectRaw("t.id as id, t.imei as imei, t.id_checador,u.nombre, u.apaterno, u.amaterno, t.marca, t.modelo, t.linea, t.id_impresora")
                    ->leftjoin("igh.usuario as u", "u.idusuario", "=","t.id_checador")
                    ->where("t.estatus","=","1")
                    ->where("t.imei", "=", $u->imei)->get();

                foreach ($datos as $r) {
                    $dat[] = [
                        'id' => $r->id,
                        'imei' => $r->imei,
                        'usuario' => $r->id_checador,
                        'nombre' => $r->nombre." ".$r->apaterno." ".$r->amaterno,
                        'marca' => $r->marca,
                        'modelo' => $r->modelo,
                        'linea' => $r->linea,
                        'impresora' => $r->id_impresora,
                        'mac'=> ""
                    ];
                }
            }
            return view('tablero-control.telefonos_detalle')->withTipo(4)->withFechaF($fecha)->withTelefono($dat);
        }else if($id == 5){
            $imei = DB::connection("sca")->table("telefonos")->where("estatus","=","1")
                ->whereNotNull("id_impresora")
                ->groupBy("imei")->havingRaw("count('imei')>1")->get();

            foreach ($imei as $u) {
                $datos = DB::connection("sca")->table("telefonos as t")
                    ->selectRaw("t.id as id, t.imei as imei, t.id_checador,u.nombre, u.apaterno, u.amaterno, t.marca, t.modelo, t.linea,t.id_impresora, i.mac")
                    ->leftjoin("igh.usuario as u", "u.idusuario", "=","t.id_checador")
                    ->leftjoin("impresoras as i", "i.id","=", "t.id_impresora")
                    ->where("t.estatus","=","1")
                    ->where("t.imei", "=", $u->imei)->get();

                foreach ($datos as $r) {
                    $dat[] = [
                        'id' => $r->id,
                        'imei' => $r->imei,
                        'usuario' => $r->id_checador,
                        'nombre' => $r->nombre." ".$r->apaterno." ".$r->amaterno,
                        'marca' => $r->marca,
                        'modelo' => $r->modelo,
                        'linea' => $r->linea,
                        'impresora' => $r->id_impresora,
                        'mac' => $r->mac
                    ];
                }
            }
            return view('tablero-control.telefonos_detalle')->withTipo(5)->withFechaF($fecha)->withTelefono($dat);
        }
        else if($id == 6){
            $impresora = DB::connection("sca")->table("telefonos")->where("estatus","=","1")
                ->whereNotNull("id_impresora")
                ->groupBy("id_impresora")->havingRaw("count('id_impresora')>1")->get();

            foreach ($impresora as $u) {
                $datos = DB::connection("sca")->table("telefonos as t")
                    ->selectRaw("t.id as id, t.imei as imei, t.id_checador,u.nombre, u.apaterno, u.amaterno, t.marca, t.modelo, t.linea, t.id_impresora, i.mac")
                    ->leftjoin("igh.usuario as u", "u.idusuario", "=","t.id_checador")
                    ->leftjoin("impresoras as i", "i.id","=", "t.id_impresora")
                    ->where("t.estatus","=","1")
                    ->where("t.id_impresora", "=", $u->id_impresora)->get();

                foreach ($datos as $r) {
                    $dat[] = [
                        'id' => $r->id,
                        'imei' => $r->imei,
                        'usuario' => $r->id_checador,
                        'nombre' => $r->nombre." ".$r->apaterno." ".$r->amaterno,
                        'marca' => $r->marca,
                        'modelo' => $r->modelo,
                        'linea' => $r->linea,
                        'impresora' => $r->id_impresora,
                        'mac' => $r->mac
                    ];
                }
            }
            return view('tablero-control.telefonos_detalle')->withTipo(6)->withFechaF($fecha)->withTelefono($dat);
        }else if($id == 7){
            $cancela = DB::connection("sca")->table("conciliacion_cancelacion")
                ->whereNotNull("estado_rol_usuario")
                ->where("estado_rol_usuario", "=", "0")->get();
            foreach ($cancela as $c){
                    $name = $this->nombre($c->idcancelo);
                    $datos[]=[
                        "id"=>$c->id,
                        "conciliacion" => $c->idconciliacion,
                        "motivo" => $c->motivo,
                        "fecha" => $c->fecha_hora_cancelacion,
                        "idcancelo" => $c->idcancelo,
                        "nombre" => $name
                    ];
            }
            return view('tablero-control.conciliacion_detalle')->withTipo(7)->withFechaF($fecha)->withConciliacion($datos);
        }
        else if($id == 8){
            $camion_manual = DB::connection("sca")->table("viajesnetos")
                ->selectRaw("IdCamion")
                ->join("folios_vales_manuales", "Code", "=", "folio")
                ->whereIn("Estatus", array('29','20','21'))
                ->whereNotNull("Code")
                ->groupBy("IdCamion")
                ->havingRaw("count(IdCamion)>1")->get();

            foreach ($camion_manual as $c){
                $viajes_manual = DB::connection("sca")->table("viajesnetos as a")
                    ->selectRaw("a.IdViajeNeto, b.Economico AS economico, o.Descripcion AS origen, t.Descripcion AS tiro,
                    m.Descripcion AS material, a.FechaSalida as fs, a.HoraSalida as hs, a.CubicacionCamion as cubicacion,
                       a.FechaLlegada as fl, a.HoraLlegada as hl, a.folioMina as mina, a.folioSeguimiento as seguimiento, 
                       a.Code, c.folio, c.id_viaje_neto as v, c.created_at as c,
                       IF(a.estatus = 29, 'Cargado',IF(a.estatus = 20,'Pendiente Validar', 'Validado')) AS estatus")
                    ->join("folios_vales_manuales as c", "a.Code", "=", "c.folio")
                    ->join("camiones as b","a.IdCamion", "=", "b.IdCamion")
                    ->join("origenes as o", "o.IdOrigen","=","a.IdOrigen")
                    ->join("tiros as t","t.IdTiro", "=", "a.IdTiro")
                    ->join("materiales as m","m.IdMaterial","=", "a.IdMaterial")
                    ->whereIn("a.Estatus", array('29','20','21'))
                    ->whereNotNull("a.Code")
                    ->whereNotNull("c.id_viaje_neto")
                    ->where("a.IdCamion", "=", $c->IdCamion)->get();

                foreach ($viajes_manual as $v){
                    $datos[]=[
                        'origen'=>$v->origen,
                        'economico' => $v->economico,
                        'tiro' =>$v->tiro,
                        'fl' =>$v->fl,
                        'hl'=>$v->hl,
                        'material' => $v->material,
                        'fs'=>$v->fs,
                        'hs'=>$v->hs,
                        'cubicacion' => $v->cubicacion,
                        'foliomina' =>$v->mina,
                        'folioseg'=>$v->seguimiento,
                        'estatus' => $v->estatus,
                        'code' => $v->Code,
                        'folio'=>$v->folio,
                        'v'=>$v->v,
                        'c'=> $v->c

                    ];
                }
            }
            return view('tablero-control.viajes_manual')->withTipo(8)->withFechaF($fecha)->withDatos($datos);

        }else if($id == 9) {
            $validacion_conciliacion = DB::connection("sca")->table("conciliacion")
                ->whereRaw("IdRegistro=IdCerro")
                ->whereRaw("IdRegistro=IdAprobo")
                ->whereRaw("IdCerro=IdAprobo")->get();
            foreach ($validacion_conciliacion as $c) {
                $name = $this->nombre($c->IdRegistro);

                $datos[] = [
                    "id" => $c->idconciliacion,
                    "conciliacion" => $c->idconciliacion,
                    "motivo" => "",
                    "fecha" => $c->fecha_conciliacion,
                    "idcancelo" => $c->IdAprobo,
                    "nombre" => $name,
                    "estado" => $c->estado
                ];
            }
            return view('tablero-control.conciliacion_detalle')->withTipo(9)->withFechaF($fecha)->withConciliacion($datos);
        }else if($id == 10){
            $camiones = DB::connection("sca")->table("camiones")
                ->where("Estatus","=","1")
                ->where("CubicacionReal", "!=", "0")
                ->where("CubicacionParaPago", "!=", "0")
                ->orderByRaw("IdCamion")
                ->get();

            $datos = [];
            foreach ($camiones as $c){
                $cubicacion = DB:: connection("sca")->table("camiones_historicos_solicitudes")
                    ->where("CubicacionReal", "!=", "0")
                    ->where("CubicacionParaPago", "!=", "0")
                    ->where("IdCamion", "=", $c->IdCamion)
                    ->where("Estatus", "=", "1")->get();

                $cubicacion_h = DB:: connection("sca")->table("camiones_historicos")
                    ->where("CubicacionReal", "!=", "0")
                    ->where("CubicacionParaPago", "!=", "0")
                    ->where("IdCamion", "=", $c->IdCamion)
                    ->where("Estatus","=", "1")->get();

                if ($cubicacion != [] && $cubicacion_h == []){
                    foreach ($cubicacion as $a){
                        $save=[];
                        $bandera = 0;
                        $bandera = $this->concidencias($save, $a);
                        if($bandera == 0){
                            $save [] =[
                                'idcamion' => $a->IdCamion,
                                'economico' => $a->Economico,
                                'placas' => $a->Placas,
                                'cubicacionPago' => $a->CubicacionParaPago,
                                'cubicacionReal' => $a->CubicacionReal,
                                'fecha' => $a->updated_at,
                                'cubicacionPagoActual' => $c->CubicacionParaPago,
                                'cubicacionRealActual' => $c->CubicacionReal
                            ];
                        }
                    }
                    $datos =  array_merge($datos, $save);
                }elseif ($cubicacion_h != [] && $cubicacion == []){
                    foreach ($cubicacion_h as $a){
                        $save=[];
                        $bandera = 0;
                        $bandera = $this->concidencias($save, $a);
                        if($bandera == 0){
                            $save [] =[
                                'idcamion' => $a->IdCamion,
                                'economico' => $a->Economico,
                                'placas' => $a->Placas,
                                'cubicacionPago' => $a->CubicacionParaPago,
                                'cubicacionReal' => $a->CubicacionReal,
                                'fecha' => $a->updated_at,
                                'cubicacionPagoActual' => $c->CubicacionParaPago,
                                'cubicacionRealActual' => $c->CubicacionReal
                            ];
                        }
                    }
                    $datos =  array_merge($datos, $save);
                }else{
                    $save=[];
                    foreach ($cubicacion as $a) {
                        $bandera = 0;
                        $bandera = $this->concidencias($save, $a);
                        if($bandera == 0){
                            $save [] =[
                                'idcamion' => $a->IdCamion,
                                'economico' => $a->Economico,
                                'placas' => $a->Placas,
                                'cubicacionPago' => $a->CubicacionParaPago,
                                'cubicacionReal' => $a->CubicacionReal,
                                'fecha' => $a->updated_at,
                                'cubicacionPagoActual' => $c->CubicacionParaPago,
                                'cubicacionRealActual' => $c->CubicacionReal
                            ];
                        }
                        $bandera = 0;
                        foreach ($cubicacion_h as $b){
                            if($a->CubicacionReal != $b->CubicacionReal && $a->CubicacionParaPago != $b->CubicacionParaPago ){
                                $bandera = $this->concidencias($save, $b);
                            }else{
                                $bandera = 1;
                            }
                            if($bandera == 0){
                                $save [] =[
                                    'idcamion' => $b->IdCamion,
                                    'economico' => $b->Economico,
                                    'placas' => $b->Placas,
                                    'cubicacionPago' => $b->CubicacionParaPago,
                                    'cubicacionReal' => $b->CubicacionReal,
                                    'fecha' => $b->updated_at,
                                    'cubicacionPagoActual' => $c->CubicacionParaPago,
                                    'cubicacionRealActual' => $c->CubicacionReal
                                ];
                            }
                        }
                    }
                    $datos =  array_merge($datos, $save);
                }
            }
            return view('tablero-control.camiones_detalle')->withTipo(10)->withFechaF($fecha)->withDatos($datos);
        }else if($id == 11){
            $datos = DB::connection("sca")->select(DB::raw("SELECT a.IdTarifa, a.IdMaterial, b.descripcion,
                  a.PrimerKM, a.KMSubsecuente, a.KMAdicional, a.Estatus, a.Fecha_Hora_Registra, a.Registra, 
                  a.InicioVigencia, a.FinVigencia, a.created_at, a.updated_at, a.idtarifas_tipo,u.nombre,u.apaterno,u.amaterno
                  FROM tarifas a
                  inner join materiales b on a.idmaterial= b.idmaterial
                  inner join igh.usuario u on u.idusuario = a.Registra
                  where a.IdMaterial in (select IdMaterial from tarifas a
                  group by IdMaterial having count(*) > 1) order by Descripcion"));

            return view('tablero-control.tarifas')->withTipo(11)->withFechaF($fecha)->withTarifas($datos);
        }else if($id == 12){
            $viajes_primer = [];
            $viajes_segundo = [];
            $camiones_primer_turno = DB::connection("sca")->select(DB::raw("SELECT count(IdCamion),IdCamion FROM viajesnetos
                            where FechaLlegada = '".$fecha."' and HoraLlegada between '07:00:00' and '19:00:00' 
                            group by IdCamion 
                            having count(IdCamion) > 3
                            order by IdCamion;"));
            $camiones_segundo_turno = DB::connection("sca")->select(DB::raw("SELECT count(IdCamion),IdCamion FROM viajesnetos
                            where (FechaLlegada = '".$ayer."' and HoraLlegada between '19:00:00' and '23:59:59') or 
                             (FechaLlegada = '".$fecha."' and HoraLlegada between '00:00:00' and '06:59:59')
                            group by IdCamion 
                            having count(IdCamion) > 3
                            order by IdCamion;"));

            foreach ($camiones_primer_turno as $a){
                $viajes_primer = DB::connection("sca")->select(DB::raw("select a.IdViajeNeto, b.Economico AS economico, o.Descripcion AS origen, t.Descripcion AS tiro,
m.Descripcion AS material, a.FechaSalida as fs, a.HoraSalida as hs, a.CubicacionCamion as cubicacion,
a.FechaLlegada as fl, a.HoraLlegada as hl, a.folioMina as mina, a.folioSeguimiento as seguimiento,
a.Code, 
IF(a.estatus = 29, 'Viaje Manual - Cargado',
IF(a.estatus = 20, 'Viaje Manual - Pendiente Validar',
IF(a.estatus = 0, 'Viaje - Pendiente por Validar',
IF(a.estatus = 1, 'Viaje - Validado', 
IF(a.estatus = 21, 'Validado',''))))) AS estatus from `viajesnetos` as `a`
inner join `camiones` as `b` on `a`.`IdCamion` = `b`.`IdCamion`
Inner join `origenes` as `o` on `o`.`IdOrigen` = `a`.`IdOrigen` 
inner join `tiros` as `t` on `t`.`IdTiro` = `a`.`IdTiro` inner join `materiales` as `m` on `m`.`IdMaterial` = `a`.`IdMaterial`
 where `a`.`FechaLlegada` = '".$fecha."' and `a`.`HoraLlegada` between '07:00:00' and '19:00:00' and `a`.`IdCamion` = '".$a->IdCamion."'"));
            }
            foreach ($camiones_segundo_turno as $c){
                $viajes_segundo = DB::connection("sca")->table("viajesnetos as a")
                    ->selectRaw("a.IdViajeNeto, b.Economico AS economico, o.Descripcion AS origen, t.Descripcion AS tiro,
                    m.Descripcion AS material, a.FechaSalida as fs, a.HoraSalida as hs, a.CubicacionCamion as cubicacion,
                       a.FechaLlegada as fl, a.HoraLlegada as hl, a.folioMina as mina, a.folioSeguimiento as seguimiento, 
                       a.Code, IF(a.estatus = 29, 'Viaje Manual - Cargado',
                        IF(a.estatus = 20, 'Viaje Manual - Pendiente Validar',
                        IF(a.estatus = 0, 'Viaje - Pendiente por Validar',
                        IF(a.estatus = 1, 'Viaje - Validado', 
                        IF(a.estatus = 21, 'Validado',''))))) AS estatus")
                    ->join("camiones as b","a.IdCamion", "=", "b.IdCamion")
                    ->join("origenes as o", "o.IdOrigen","=","a.IdOrigen")
                    ->join("tiros as t","t.IdTiro", "=", "a.IdTiro")
                    ->join("materiales as m","m.IdMaterial","=", "a.IdMaterial")
                    ->whereRaw("(a.FechaLlegada = '".$ayer."' and a.HoraLlegada between '19:00:00' and '23:59:59')
                           or (a.FechaLlegada =  '".$fecha."' and a.HoraLlegada between '00:00:00' and '06:59:59')")
                    ->where("a.IdCamion", "=", $a->IdCamion)->get();
            }
            return view('tablero-control.camiones_detalle')->withTipo(12)->withFechaF($fecha)->withPrimerTurno($viajes_primer)->withSegundoTurno($viajes_segundo);
        }else if($id == 13){

        }
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

    public  function sumar ($datos){
        $suma = 0;
        if($datos !="") {
            foreach ($datos as $d) {
                $suma = $suma + $d->total;
            }
        }
        return $suma;

    }
    public function nombre ($idusuario){
        $nom = DB::connection("sca")->table("igh.usuario")
            ->where("idusuario", "=", $idusuario)->get();
        foreach ($nom as $n) {
            $nombre = $n->nombre . " " . $n->apaterno . " " . $n->amaterno;
        }
        return $nombre;
    }

    public function concidencias($save, $c){
        $bandera = 0;
        if($save != []) {
            foreach ($save as $s) {
                if ($s["idcamion"] == $c->IdCamion) {
                    if ($s["cubicacionReal"] == $c->CubicacionReal || $s["cubicacionPago"] == $c->CubicacionParaPago) {
                        $bandera = $bandera + 1;
                    }
                }
            }
        }
        return $bandera;
    }

    public function ordenar($data){
        $ceros = [];
        $mayores = [];

        foreach ($data as $i){
            if($i[1] == 0){
                $ceros [] = $i;
            }
            else{
                $mayores [] = $i;
            }
        }
        foreach ($mayores as $m){
            $datos [] = $m;
        }
        foreach ($ceros as $c){
            $datos [] = $c;
        }
        return $datos;
    }
}