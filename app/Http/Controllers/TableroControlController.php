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
        //dd($inicioFecha);

        // Viajes no validados y no conciliados.
        $novalidados = DB::connection("sca")->table("viajesnetos as v")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereBetween("v.FechaLlegada",[$inicioFecha,$fecha])
            ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")->count();

        $novalidados_total = DB::connection("sca")->table("viajesnetos as v")
            ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->whereRaw("v.FechaLlegada < '".$inicioFecha."'")
            ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")->count();

        // Viajes validados y no conciliados.
        $validados = DB::connection("sca")->table("viajesnetos as v")->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
            ->whereBetween("v.FechaLlegada",[$inicioFecha,$fecha])->whereIn("v.Estatus",array('1','21'))
            ->whereNotNull("vr.IdViaje")
            ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")->count();

        $validados_total = DB::connection("sca")->table("viajesnetos as v")->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
            ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
            ->whereRaw("v.FechaLlegada < '".$inicioFecha."'")
            ->whereIn("v.Estatus",array('1','21'))
            ->whereNotNull("vr.IdViaje")
            ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")->count();

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

        $cancela = DB::connection("sca")->table("conciliacion_cancelacion")->get();
        $sum = 0;
        foreach ($cancela as $c){
            $usuario = DB::connection("sca")->table("sca_configuracion.role_user")
                ->where("user_id","=",$c->idcancelo)
                ->where("role_id", "=", "3")
                ->where("id_proyecto","=",Context::getId())->get();
            if($usuario == []){
               $sum = $sum + 1;
            }
        }

        return view('tablero-control.index')
                ->withNoValidados($novalidados)
                ->withNoValidadosTotal($novalidados_total)
                ->withValidados($validados)
                ->withValidadosTotal($validados_total)
                ->withUsuarioImei($usuarios_imei)
                ->withImeiUsuario($imei_usuario)
                ->withImpresoraImei($impresora_imei)
                ->withImeiImpresora($imei_impresora)
                ->withConciliacionCancelar($sum);
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
        $fecha = date('Y-m-d');
        $inicioFecha = strtotime('-7 day', strtotime($fecha));
        $inicioFecha = date('Y-m-d', $inicioFecha);
        $busqueda = $request->get('buscar');
        if($id == 1){ //no validados y no conciliados
            $novalidados = DB::connection("sca")->table("viajesnetos as v")
                ->selectRaw("v.IdCamion, c.Economico AS economico, v.idorigen, o.Descripcion AS origen, v.FechaSalida AS fs, v.HoraSalida AS hs,
                    v.CubicacionCamion AS cubicacion, v.IdTiro, t.Descripcion AS tiro, v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                    m.Descripcion AS material, v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, IF(v.FechaLlegada >= '".$inicioFecha."','0','1') AS alerta")
                ->leftjoin("viajesrechazados as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->join("camiones as c", "c.IdCamion", "=", "v.IdCamion")
                ->join("origenes as o", "o.IdOrigen","=","v.IdOrigen")
                ->join("tiros as t","t.IdTiro", "=", "v.IdTiro")
                ->join("materiales as m","m.IdMaterial","=", "v.IdMaterial")
                ->whereIn("v.Estatus",array('0','29','20'))->whereNull("vr.IdViajeRechazado")
                ->orderBy("v.FechaLlegada","desc");

            return view('tablero-control.detalle_no_validado')->withTipo(1)->withFechaF($fecha)->withDatos($novalidados->paginate(100))->withBusqueda($busqueda);

        }else if ($id == 2){
            $validados = DB::connection("sca")->table("viajesnetos as v")
                ->selectRaw("v.IdCamion, c.Economico AS economico, v.idorigen, o.Descripcion AS origen, v.FechaSalida AS fs, v.HoraSalida AS hs,
                    v.CubicacionCamion AS cubicacion, v.IdTiro, t.Descripcion AS tiro, v.FechaLlegada AS fl, v.HoraLlegada AS hl, v.IdMaterial,
                    m.Descripcion AS material, v.Code AS code, v.folioMina AS foliomina, v.folioSeguimiento AS folioseg, IF(v.FechaLlegada >= '".$inicioFecha."','0','1') AS alerta")
                ->leftjoin("viajes as vr","vr.IdViajeNeto", "=","v.IdViajeNeto")
                ->leftjoin("conciliacion_detalle as cd","cd.idviaje_neto", "=","v.IdViajeNeto")
                ->join("camiones as c", "c.IdCamion", "=", "v.IdCamion")
                ->join("origenes as o", "o.IdOrigen","=","v.IdOrigen")
                ->join("tiros as t","t.IdTiro", "=", "v.IdTiro")
                ->join("materiales as m","m.IdMaterial","=", "v.IdMaterial")
                ->whereIn("v.Estatus",array('1','21'))
                ->whereNotNull("vr.IdViaje")
                ->whereRaw("(cd.idconciliacion_detalle IS NULL or cd.estado = -1)")
                ->orderBy("v.FechaLlegada","desc");
            return view('tablero-control.detalle_no_validado')->withTipo(2)->withFechaF($fecha)->withDatos($validados->paginate(100))->withBusqueda($busqueda);
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
            $cancela = DB::connection("sca")->table("conciliacion_cancelacion")->get();
            foreach ($cancela as $c){
                $usuario = DB::connection("sca")->table("sca_configuracion.role_user")
                    ->where("user_id","=",$c->idcancelo)
                    ->where("role_id", "=", "3")
                    ->where("id_proyecto","=",Context::getId())->get();

                if($usuario == []){
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
            } //dd($datos);
            return view('tablero-control.conciliacion_detalle')->withTipo(7)->withFechaF($fecha)->withConciliacion($datos);
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
}