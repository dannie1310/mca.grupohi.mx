<?php
/**
 * Created by PhpStorm.
 * User: DBENITEZ
 * Date: 14/06/2017
 * Time: 07:39 PM
 */

namespace App\Models\Transformers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Themsaid\Transformers\AbstractTransformer;

class InicioViajeReporteTransformer extends AbstractTransformer
{
    public static function toArray(Request $request, $horaInicial, $horaFinal)
    {

        $timestamp_inicial = $request->get('FechaInicial') . ' ' . $horaInicial;
        $timestamp_final = $request->get('FechaFinal') . ' ' . $horaFinal;


        $SQL = "SELECT distinct 
                i.fecha_origen AS fechaorigen,
                CONCAT(u.nombre, ' ', u.apaterno, ' ', u.amaterno) as usuario,
                c.Economico as camion,
                c.Placas as placas,
                c.CubicacionParaPago as cubicacion,
                m.Descripcion as material,
                o.Descripcion as origen,
                p.name as perfil,
                i.folioMina,
                i.folioSeguimiento,
                i.code,
                i.tipo,
                case when i.tipo = 1 then 'Origen (Mina)' else 'Entrada' end as tipo_viaje,
                i.deductiva,
                i.idMotivo_deductiva,
                dm.motivo,
                case 
                when (hour(i.fecha_origen) >= '07:00:00' and hour(i.fecha_origen) < '19:00:00')  then 'Primer Turno'
                else 'Segundo Turno'
                end as turno
                FROM prod_sca_pista_aeropuerto_2.inicio_camion i
                inner join camiones c on c.IdCamion = i.idcamion
                inner join materiales m on m.IdMaterial = i.idmaterial
                inner join origenes o on o.IdOrigen = i.idorigen
                left join usuario u on u.idusuario = i.idusuario
                left join configuracion_perfiles_cat as p on p.id = i.idperfil
                left join deductivas_motivos as dm on dm.id = i.idMotivo_deductiva
                where i.fecha_origen between '{$timestamp_inicial}' and '{$timestamp_final}'
                group by i.id
                order by fechaorigen,  camion";

        return DB::connection('sca')->select(DB::raw($SQL));
    }
}