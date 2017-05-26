<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class HistoricoController extends Controller
{

    function __construct() {
        $this->middleware('auth');
        $this->middleware('context');
        parent::__construct();
    }

    public function centroscosto($id){
        $encabezado = ["ID", "Descripción", "Cuenta", "Centro de Costo Padre", "Fecha y hora registro", "Registró", "Estatus"];
        $rows = CentroCosto::leftJoin("centroscosto as padres", "centroscosto.IdPadre", "=", "padres.IdCentroCosto")
            ->leftJoin("igh.usuario as usuario", "centroscosto.usuario_registro", "=", "usuario.idusuario")
            ->select(
                "centroscosto.IdCentroCosto",
                "centroscosto.Descripcion",
                "centroscosto.Cuenta",
                "padres.Descripcion as padre",
                "centroscosto.created_at",
                "usuario.nombre",
                "centroscosto.Estatus"
            )->where('IdCentroCosto','=',$id)
            ->orderBy("centroscosto.IdCentroCosto", "ASC")
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);
    }

    public function camiones($id){
        $encabezado = ["#", "Economico", "Sindicato", "Empresa", "Placas del Camión", "Placas de la Caja", "Marca", "Modelo", "Propietario", "Operador", "Aseguradora", "Poliza de Seguro", "Vigencia Seguro", "Cubicación Real", "Cubicación Para Pago", "Estatus", "Registró", "Fecha y Hora Registro", "Desactivo", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = DB::connection('sca')->table('camiones_historicos as camiones')
            ->leftJoin('operadores', 'camiones.IdOperador', '=', 'operadores.IdOperador')
            ->leftJoin('sindicatos', 'camiones.IdSindicato', '=', 'sindicatos.IdSindicato')
            ->leftJoin('empresas', 'camiones.IdEmpresa', '=', 'empresas.IdEmpresa')
            ->leftJoin('marcas', 'camiones.IdMarca', '=', 'marcas.IdMarca')
            ->leftJoin("igh.usuario as usuario_reg", "camiones.usuario_registro", "=", "usuario_reg.idusuario")
            ->leftJoin("igh.usuario as usuario_elim", "camiones.usuario_desactivo", "=", "usuario_elim.idusuario")
            ->select(
                "camiones.Economico",
                "sindicatos.Descripcion as Sindicato",
                "empresas.razonSocial as Empresa",
                "camiones.Placas",
                "camiones.PlacasCaja",
                "marcas.Descripcion as Marca",
                "camiones.Modelo",
                "camiones.Propietario",
                "operadores.Nombre",
                "camiones.Aseguradora",
                "camiones.PolizaSeguro",
                "camiones.VigenciaPolizaSeguro",
                "camiones.CubicacionReal",
                "camiones.CubicacionParaPago",
                "camiones.Estatus",
                DB::raw("CONCAT(usuario_reg.nombre, ' ', usuario_reg.apaterno, ' ', usuario_reg.amaterno)"),
                "camiones.created_at",
                DB::raw("CONCAT(usuario_elim.nombre, ' ', usuario_elim.apaterno, ' ', usuario_elim.amaterno)"),
                "camiones.updated_at",
                "camiones.motivo"
            )->where('IdCamion','=',$id)
            ->get();
        $datos = [];
        foreach($rows as $key => $row) {
            $array = (array) $row;
            $arr = [];
            foreach($array as $i => $a) {
                array_push($arr, $a);
            }
            array_push($datos, $arr);
        }

        $data =['headers'=>$encabezado,'rows'=>$datos, 'catalogo' => 'CAMIONES'];

        return view('historico', $data);


    }
    public function empresas($id){

        $encabezado = ["#", "ID", "Razón Social", "RFC", "Estatus", "Registró", "Fecha y Hora de Registro", "Desactivó", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = DB::connection('sca')
            ->table('empresas_historicos as empresas')
            ->leftJoin("igh.usuario as usuario_reg", "empresas.usuario_registro", "=", "usuario_reg.idusuario")
            ->leftJoin("igh.usuario as usuario_elim", "empresas.usuario_desactivo", "=", "usuario_elim.idusuario")
            ->select("empresas.IdEmpresa as id", "empresas.razonSocial", "empresas.RFC", "empresas.Estatus",
                DB::raw("CONCAT(usuario_reg.nombre, ' ', usuario_reg.apaterno, ' ', usuario_reg.amaterno)"),
                "empresas.created_at",
                DB::raw("CONCAT(usuario_elim.nombre, ' ', usuario_elim.apaterno, ' ', usuario_elim.amaterno)"),
                "empresas.updated_at",
                "empresas.motivo"
            )->where('empresas.IdEmpresa','=',$id)
            ->get();

        $datos = [];
        foreach($rows as $key => $row) {
            $array = (array) $row;
            $arr = [];
            foreach($array as $i => $a) {
                array_push($arr, $a);
            }
            array_push($datos, $arr);
        }

        $data =['headers'=>$encabezado,'rows'=>$datos, 'catalogo' => 'EMPRESAS'];
        return view('historico', $data);
    }

    public function etapasproyectos($id){

        $encabezado = ["#", "ID", "Descripción", "Estatus","Registró", "Fecha y Hora de Registro", "Desactivó", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = DB::connection('sca')->table('etapasproyectos_historicos as etapasproyectos')
            ->leftJoin("igh.usuario as usuario_reg", "etapasproyectos.usuario_registro", "=", "usuario_reg.idusuario")
            ->leftJoin("igh.usuario as usuario_elim", "etapasproyectos.usuario_desactivo", "=", "usuario_elim.idusuario")
            ->select("IdEtapaProyecto", "Descripcion", "Estatus",
                DB::raw("CONCAT(usuario_reg.nombre, ' ', usuario_reg.apaterno, ' ', usuario_reg.amaterno)"),
                "etapasproyectos.created_at",
                DB::raw("CONCAT(usuario_elim.nombre, ' ', usuario_elim.apaterno, ' ', usuario_elim.amaterno)"),
                "etapasproyectos.updated_at",
                "etapasproyectos.motivo")->where('etapasproyectos.IdEtapaProyecto','=',$id)
            ->get();

        $datos = [];
        foreach($rows as $key => $row) {
            $array = (array) $row;
            $arr = [];
            foreach($array as $i => $a) {
                array_push($arr, $a);
            }
            array_push($datos, $arr);
        }

        $data =['headers'=>$encabezado,'rows'=>$datos, 'catalogo' => 'ETAPAS DEL PROYECTO'];
        return view('historico', $data);
    }

    public function factorabundamiento($id){
    }
    public function factorabundamiento_material($id){

        $encabezado = ["ID", "Material", "Factor de Abundamiento", "Fecha y Hora Registro", "Estatus", "Registró"];
        $rows = FDAMaterial::leftJoin("materiales", "factorabundamiento.IdMaterial", "=", "materiales.IdMaterial")
            ->leftJoin("igh.usuario", "factorabundamiento.Registra", "=", "igh.usuario.usuario")
            ->select(
                "factorabundamiento.IdFactorAbundamiento",
                "materiales.Descripcion as Material",
                "factorabundamiento.FactorAbundamiento",
                "factorabundamiento.TimestampAlta",
                "factorabundamiento.Estatus",
                DB::raw("CONCAT(igh.usuario.nombre, ' ', igh.usuario.apaterno, ' ', igh.usuario.amaterno)"))
            ->where('IdMaterial','=',$id)
            ->get();

        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);

    }
    public function impresoras($id){

        $encabezado = ["#", "ID", "MAC Address", "Marca", "Modelo", "Estatus", "Registró", "Fecha y Hora Registro", "Desactivo", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = DB::connection('sca')->table('impresoras_historicos as impresoras')
            ->leftjoin('igh.usuario as user_registro', 'impresoras.registro', '=', 'user_registro.idusuario')
            ->leftjoin('igh.usuario as user_elimino', 'impresoras.elimino', '=', 'user_elimino.idusuario')
            ->select(
                "impresoras.id",
                "impresoras.mac",
                "impresoras.marca",
                "impresoras.modelo",
                "impresoras.estatus",
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno)"),
                "impresoras.created_at",
                DB::raw("CONCAT(user_elimino.nombre, ' ', user_elimino.apaterno, ' ', user_elimino.amaterno)"),
                "impresoras.updated_at",
                "impresoras.motivo")->where('impresoras.id','=',$id)->get();
        $datos = [];
        foreach($rows as $key => $row) {
            $array = (array) $row;
            $arr = [];
            foreach($array as $i => $a) {
                array_push($arr, $a);
            }
            array_push($datos, $arr);
        }

        $data =['headers'=>$encabezado,'rows'=>$datos, 'catalogo' => 'IMPRESORAS'];
        return view('historico', $data);
    }

    public function marcas($id){

        $encabezado = ["#", "ID", "Descripción", "Estatus","Registró", "Fecha y Hora de Registro", "Desactivó", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];

        $rows = DB::connection('sca')->table('marcas_historicos as marcas')
            ->leftJoin("igh.usuario as usuario_reg", "marcas.usuario_registro", "=", "usuario_reg.idusuario")
            ->leftJoin("igh.usuario as usuario_elim", "marcas.usuario_desactivo", "=", "usuario_elim.idusuario")
            ->select("IdMarca", "Descripcion", "Estatus",
                DB::raw("CONCAT(usuario_reg.nombre, ' ', usuario_reg.apaterno, ' ', usuario_reg.amaterno)"),
                "marcas.created_at",
                DB::raw("CONCAT(usuario_elim.nombre, ' ', usuario_elim.apaterno, ' ', usuario_elim.amaterno)"),
                "marcas.updated_at",
                "marcas.motivo")->where('marcas.IdMarca','=',$id)
            ->get();
        $datos = [];
        foreach($rows as $key => $row) {
            $array = (array) $row;
            $arr = [];
            foreach($array as $i => $a) {
                array_push($arr, $a);
            }
            array_push($datos, $arr);
        }

        $data =['headers'=>$encabezado,'rows'=>$datos, 'catalogo' => 'MARCAS'];
        return view('historico', $data);
    }

    public function materiales($id){

        $encabezado = ["#", "ID", "Descripción", "Estatus", "Registró", "Fecha y Hora de Registro", "Desactivó", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = DB::connection('sca')->table('materiales_historicos as materiales')
            ->leftJoin('igh.usuario as user_registro', 'materiales.usuario_registro', '=', 'user_registro.idusuario')
            ->leftJoin('igh.usuario as user_desactivo', 'materiales.usuario_desactivo', '=', 'user_desactivo.idusuario')
            ->select(
                "IdMaterial",
                "Descripcion",
                "Estatus",
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno)"),
                "materiales.created_at",
                DB::raw("CONCAT(user_desactivo.nombre, ' ', user_desactivo.apaterno, ' ', user_desactivo.amaterno)"),
                DB::raw("IF(materiales.Estatus = 1, '', materiales.updated_at)"),
                "materiales.motivo"
            )->where('materiales.IdMaterial','=',$id)
            ->get();
        $datos = [];
        foreach($rows as $key => $row) {
            $array = (array) $row;
            $arr = [];
            foreach($array as $i => $a) {
                array_push($arr, $a);
            }
            array_push($datos, $arr);
        }

        $data =['headers'=>$encabezado,'rows'=>$datos, 'catalogo' => 'MATERIALES'];
        return view('historico', $data);
    }

    public function operadores($id){
        $encabezado = ["#", "ID", "Nombre", "Dirección", "Número de Licencia", "Vigencia de Licencia", "Fecha Registro", "Fecha de Baja", "Estatus","Registró", "Fecha y Hora de Registro", "Desactivó", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = DB::connection('sca')->table('operadores_historicos as operadores')
            ->leftJoin("igh.usuario as usuario_reg", "operadores.usuario_registro", "=", "usuario_reg.idusuario")
            ->leftJoin("igh.usuario as usuario_elim", "operadores.usuario_desactivo", "=", "usuario_elim.idusuario")
            ->select(
                "operadores.IdOperador", "operadores.Nombre",
                "operadores.Direccion", "operadores.NoLicencia",
                "operadores.VigenciaLicencia", "operadores.FechaAlta", "operadores.FechaBaja", "operadores.Estatus",
                DB::raw("CONCAT(usuario_reg.nombre, ' ', usuario_reg.apaterno, ' ', usuario_reg.amaterno)"),
                "operadores.created_at",
                DB::raw("CONCAT(usuario_elim.nombre, ' ', usuario_elim.apaterno, ' ', usuario_elim.amaterno)"),
                "operadores.updated_at",
                "operadores.motivo")->where('operadores.IdOperador','=',$id)
            ->get();
        $datos = [];
        foreach($rows as $key => $row) {
            $array = (array) $row;
            $arr = [];
            foreach($array as $i => $a) {
                array_push($arr, $a);
            }
            array_push($datos, $arr);
        }

        $data =['headers'=>$encabezado,'rows'=>$datos, 'catalogo' => 'OPERADORES'];
        return view('historico', $data);
    }

    public function origenes($id){

        $encabezado = ["Clave", "Tipo", "Descripción", "Estatus", "Registró", "Fecha y Hora Registro", "Desactivo", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];

        $rows = Origen::
        leftJoin('igh.usuario as user_registro', 'origenes.usuario_registro', '=', 'user_registro.idusuario')
            ->leftJoin('igh.usuario as user_desactivo', 'origenes.usuario_desactivo', '=', 'user_desactivo.idusuario')
            ->leftJoin('tiposorigenes', 'origenes.IdTipoOrigen', '=', 'tiposorigenes.IdTipoOrigen')
            ->select(
                DB::raw("CONCAT(origenes.Clave,origenes.IdOrigen)"),
                "tiposorigenes.Descripcion as Tipo",
                "origenes.Descripcion",
                "origenes.Estatus",
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno)"),
                "origenes.created_at",
                DB::raw("CONCAT(user_desactivo.nombre, ' ', user_desactivo.apaterno, ' ', user_desactivo.amaterno)"),
                DB::raw("IF(origenes.Estatus = 1, '', origenes.updated_at)"),
                "origenes.motivo")->where('IdOrigen','=',$id)
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);


    }
    public function origen_x_usuario($id){




    }
    public function rutas($id){

        $encabezado = ['Clave', 'Origen', 'Tiro', 'Tipo de Ruta', '1er. KM', 'KM Subsecuentes', 'KM Adicionales', 'KM Total', 'Tiempo Minimo', 'Tiempo Tolerancia', "Estatus", "Registró", "Fecha y Hora Registro", "Desactivo", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = Ruta::leftJoin('origenes', 'rutas.IdOrigen', '=', 'origenes.IdOrigen')
            ->leftJoin('tiros', 'rutas.IdTiro', '=', 'tiros.IdTiro')
            ->leftJoin('tipo_ruta', 'rutas.IdTipoRuta', '=', 'tipo_ruta.IdTipoRuta')
            ->leftJoin('cronometrias', 'rutas.IdRuta', '=', 'cronometrias.IdRuta')
            ->leftJoin('igh.usuario as user_registro', 'rutas.usuario_registro', '=', 'user_registro.idusuario')
            ->leftJoin('igh.usuario as user_desactivo', 'rutas.usuario_desactivo', '=', 'user_desactivo.idusuario')
            ->select(
                DB::raw("CONCAT(rutas.Clave,rutas.IdRuta)"),
                "origenes.Descripcion as Origen",
                "tiros.Descripcion as Tiro",
                "tipo_ruta.Descripcion as Tipo",
                "rutas.PrimerKm",
                "rutas.KmSubsecuentes",
                "rutas.KmAdicionales",
                "rutas.TotalKM",
                "cronometrias.TiempoMinimo",
                "cronometrias.Tolerancia",
                DB::raw("IF(rutas.Estatus = 1, 'ACTIVA', 'INACTIVA')"),
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno)"),
                "rutas.created_at",
                DB::raw("CONCAT(user_desactivo.nombre, ' ', user_desactivo.apaterno, ' ', user_desactivo.amaterno)"),
                DB::raw("IF(rutas.Estatus = 1, '', rutas.updated_at)"),
                "rutas.motivo")->where('IdRuta','=',$id)
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);


    }
    public function sindicatos($id){

        $encabezado = ["ID", "Descripción", "Nombre Corto", "Estatus","Registró", "Fecha y Hora de Registro", "Desactivó", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = Sindicato:: leftJoin("igh.usuario as usuario_reg", "sindicatos.usuario_registro", "=", "usuario_reg.idusuario")
            ->leftJoin("igh.usuario as usuario_elim", "sindicatos.usuario_desactivo", "=", "usuario_elim.idusuario")
            ->select("IdSindicato", "Descripcion", "NombreCorto", "Estatus",
                DB::raw("CONCAT(usuario_reg.nombre, ' ', usuario_reg.apaterno, ' ', usuario_reg.amaterno)"),
                "sindicatos.created_at",
                DB::raw("CONCAT(usuario_elim.nombre, ' ', usuario_elim.apaterno, ' ', usuario_elim.amaterno)"),
                "sindicatos.updated_at",
                "sindicatos.motivo")->where('IdSindicato','=',$id)
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);


    }
    public function telefonos($id){
        $encabezado = ["ID", "IMEI Teléfono", "Linea Telefónica", "Marca", "Modelo", "Estatus", "Registró", "Fecha y Hora de Registro", "Desactivó", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = Telefono::leftjoin('igh.usuario as user_registro', 'telefonos.registro', '=', 'user_registro.idusuario')
            ->leftjoin('igh.usuario as user_elimino', 'telefonos.elimino', '=', 'user_elimino.idusuario')
            ->select(
                "telefonos.id",
                "telefonos.imei",
                "telefonos.linea",
                "telefonos.marca",
                "telefonos.modelo",
                "telefonos.estatus",
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno)"),
                "telefonos.created_at",
                DB::raw("CONCAT(user_elimino.nombre, ' ', user_elimino.apaterno, ' ', user_elimino.amaterno)"),
                "telefonos.updated_at",
                "telefonos.motivo")->where('id','=',$id)
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);



    }
    public function tiros($id){

        $encabezado = ["Clave", "Descripción", "Estatus", "Registró", "Fecha y Hora Registro", "Desactivo", "Fecha y Hora de Desactivación", "Motivo de Desactivación"];
        $rows = Tiro::
        leftJoin('igh.usuario as user_registro', 'tiros.usuario_registro', '=', 'user_registro.idusuario')
            ->leftJoin('igh.usuario as user_desactivo', 'tiros.usuario_desactivo', '=', 'user_desactivo.idusuario')
            ->select(
                DB::raw("CONCAT(tiros.Clave,tiros.IdTiro)"),
                "tiros.Descripcion",
                "tiros.Estatus",
                DB::raw("CONCAT(user_registro.nombre, ' ', user_registro.apaterno, ' ', user_registro.amaterno)"),
                "tiros.created_at",
                DB::raw("CONCAT(user_desactivo.nombre, ' ', user_desactivo.apaterno, ' ', user_desactivo.amaterno)"),
                DB::raw("IF(tiros.Estatus = 1, '', tiros.updated_at)"),
                "tiros.motivo")->where('IdTiro','=',$id)
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);


    }
    public function tarifas($id){
        $headers = ["ID", "Material", "Primer KM", "KM Subsecuente", "KM Adicional", "Fecha y Hora Registro", "Estatus", "Registra", "Inicio Vigencia", "Fin Vigencia"];
        $items = TarifaMaterial::leftJoin('materiales', 'tarifas.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario', 'tarifas.Registra', '=', 'igh.usuario.idusuario')
            ->select(
                "tarifas.IdTarifa",
                "materiales.Descripcion",
                "tarifas.PrimerKM",
                "tarifas.KMSubsecuente",
                "tarifas.KMAdicional",
                "tarifas.Fecha_Hora_Registra",
                "tarifas.Estatus",
                DB::raw("CONCAT(igh.usuario.nombre, ' ', igh.usuario.apaterno, ' ', igh.usuario.amaterno)"),
                "tarifas.InicioVigencia",
                "tarifas.FinVigencia")
            ->get();
        $csv = new CSV($headers, $items);
        $csv->generate('tarifas_material');



    }

    public function tarifas_material()
    {
        $encabezado = ["ID", "Material", "Primer KM", "KM Subsecuente", "KM Adicional", "Fecha y Hora Registro", "Estatus", "Registra", "Inicio Vigencia", "Fin Vigencia"];
        $rows = TarifaMaterial::leftJoin('materiales', 'tarifas.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario', 'tarifas.Registra', '=', 'igh.usuario.idusuario')
            ->select(
                "tarifas.IdTarifa",
                "materiales.Descripcion",
                "tarifas.PrimerKM",
                "tarifas.KMSubsecuente",
                "tarifas.KMAdicional",
                "tarifas.Fecha_Hora_Registra",
                "tarifas.Estatus",
                DB::raw("CONCAT(igh.usuario.nombre, ' ', igh.usuario.apaterno, ' ', igh.usuario.amaterno)"),
                "tarifas.InicioVigencia",
                "tarifas.FinVigencia")->where('IdTiro','=',$id)
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);
    }

    public function tarifas_peso($id)
    {
        $headers = ["ID", "Material", "Primer KM", "KM Subsecuente", "KM Adicional", "Fecha y Hora Registro", "Estatus", "Registró"];
        $items = TarifaPeso::leftJoin('materiales', 'tarifas_peso.IdMaterial', '=', 'materiales.IdMaterial')
            ->leftJoin('igh.usuario', 'tarifas_peso.Registra', '=', 'igh.usuario.idusuario')
            ->select(
                "tarifas_peso.IdTarifa",
                "materiales.Descripcion as Material",
                "tarifas_peso.PrimerKM",
                "tarifas_peso.KMSubsecuente",
                "tarifas_peso.KMAdicional",
                "tarifas_peso.Fecha_Hora_Registra",
                "tarifas_peso.Estatus",
                DB::raw("CONCAT(igh.usuario.nombre, ' ', igh.usuario.apaterno, ' ', igh.usuario.amaterno)")
            )->where('IdTarifa','=',$id)
            ->get();
        $data=['encabezado'=>$encabezado,'rows'=>$rows];
        return view('historico.Historico')
            ->with('data',$data);
    }
}
