<?php
/**
 * Created by PhpStorm.
 * User: JFEsquivel
 * Date: 28/03/2017
 * Time: 05:44 PM
 */

namespace App\Models\Transformers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Themsaid\Transformers\AbstractTransformer;

class ViajeNetoReporteTransformer extends AbstractTransformer
{
    public static function toArray(Request $request, $horaInicial, $horaFinal, $estatus) {

        $timestamp_inicial = $request->get('FechaInicial') . ' ' . $horaInicial;
        $timestamp_final = $request->get('FechaFinal') . ' ' . $horaFinal;


        $SQL = "SELECT
      DATE_FORMAT(v.FechaLlegada, '%d-%m-%Y') AS Fecha,
       DATE_FORMAT(v.FechaSalida, '%d-%m-%Y') AS FechaSalida,
       DATE_FORMAT(v.HoraSalida, '%h:%i:%s') AS HoraSalida,
      t.IdTiro,
      t.Descripcion AS Tiro,
      c.IdCamion AS IdCamion,
      c.Economico AS Camion,
      v.IdViajeNeto as IdViajeNeto,
      v.estatus as idEstatus,
      v.code,
      CASE 
        WHEN v.estatus in (1,11,21,31) THEN 'Validado'
        WHEN v.estatus in (0,10,20,30) THEN 'Pendiente de Validar'
      END AS Estatus,
      CONCAT(user_primer.nombre, ' ', user_primer.apaterno, ' ', user_primer.amaterno) as primer_toque,
      CONCAT(user_segundo.nombre, ' ', user_segundo.apaterno, ' ', user_segundo.amaterno) as segundo_toque,
      IF(v.HoraLlegada >= '07:00:00' AND v.HoraLlegada < '19:00:00', 'Primer Turno', 'Segundo Turno') as turno,
      v.HoraLlegada as Hora,
      v.code,
      c.CubicacionParaPago as cubicacion,
      v.CubicacionCamion as CubicacionViajeNeto,
      vi.CubicacionCamion as CubicacionViaje,
      o.Descripcion as origen,
      o.IdOrigen as idorigen,
      m.Descripcion as material,
      m.IdMaterial as idmaterial,
      sin.Descripcion as Sindicato,
      sinca.Descripcion as SindicatoCamion,
      emp.razonSocial as Empresa,
      sincon.Descripcion as SindicatoConci,
      empcon.razonSocial as Empresaconci,
      TIMEDIFF( (CONCAT(v.FechaLlegada,' ',v.HoraLlegada)),(CONCAT(v.FechaSalida,' ',v.HoraSalida)) ) as tiempo_mostrar,
      ROUND((HOUR(TIMEDIFF(v.HoraLlegada,v.HoraSalida))*60)+(MINUTE(TIMEDIFF(v.HoraLlegada,v.HoraSalida)))+(SECOND(TIMEDIFF(v.HoraLlegada,v.HoraSalida))/60),2) AS tiempo,
      concat('R-',r.IdRuta) as ruta,
      r.TotalKM as distancia,
      r.IdRuta as idruta,
      tm.IdTarifa as tarifa_material,
      tm.PrimerKM as tarifa_material_pk,
      tm.KMSubsecuente as tarifa_material_ks,
      tm.KMAdicional as tarifa_material_ka,
      ((tm.PrimerKM*1*c.CubicacionParaPago)+(tm.KMSubsecuente*r.KmSubsecuentes*c.CubicacionParaPago)+(tm.KMAdicional*r.KmAdicionales*c.CubicacionParaPago)) as ImporteTotal_M,
      conci.idconciliacion,
      conci.fecha_conciliacion,
      conci.fecha_inicial,
      conci.fecha_final,
      conci.estado,
      IF(conci.estado < 0, 'CALCELADA', IF(conci.estado = 0, 'GENERADA', IF(conci.estado = 1, 'CERRADA', IF(conci.estado = 2, 'APROBADA', '')))) as estado_string,
      vi.IdViaje,
      c.placas,
      c.PlacasCaja,
      v.CreoPrimerToque,
      v.Creo,
      cev.identifiacador as conflictos,
      v.imei,
      cpc.name as perfil,
      cpc.id as IdPerfil
      FROM
        viajesnetos AS v
      JOIN tiros AS t USING (IdTiro)
      JOIN camiones AS c USING (IdCamion)
      left join origenes as o using(IdOrigen) 
      join materiales as m using(IdMaterial) 
      left join tarifas as tm on(tm.IdMaterial=m.IdMaterial AND tm.Estatus=1) 
      left join rutas as r on(v.IdOrigen=r.IdOrigen AND v.IdTiro=r.IdTiro AND r.Estatus=1) 
      left join sindicatos as sinca on sinca.IdSindicato = c.IdSindicato
      LEFT JOIN viajes AS vi ON vi.IdViajeNeto = v.IdViajeNeto
      left join sindicatos as sin on sin.IdSindicato = vi.IdSindicato
      left join empresas as emp on emp.IdEmpresa = vi.IdEmpresa
      LEFT JOIN conciliacion_detalle AS conde ON (conde.idviaje =  vi.IdViaje AND conde.estado = 1)
      LEFT JOIN conciliacion as conci ON conci.idconciliacion = conde.idconciliacion 
      left join sindicatos as sincon on sincon.IdSindicato = conci.IdSindicato
      left join igh.usuario as user_primer on v.CreoPrimerToque = user_primer.idusuario
      left join igh.usuario as user_segundo on v.Creo = user_segundo.idusuario
      left join empresas as empcon on empcon.IdEmpresa = conci.IdEmpresa
      left join configuracion_perfiles_cat as cpc on cpc.id = v.IdPerfil
       left join (      
      SELECT conflictos_entre_viajes_detalle.idviaje_neto,
      
       conflictos_entre_viajes.id as idconflicto
  FROM (((conflictos_entre_viajes_detalle conflictos_entre_viajes_detalle_1
          INNER JOIN
          conflictos_entre_viajes conflictos_entre_viajes
             ON (conflictos_entre_viajes_detalle_1.idconflicto =
                    conflictos_entre_viajes.id))
         INNER JOIN
         conflictos_entre_viajes_detalle conflictos_entre_viajes_detalle
            ON (conflictos_entre_viajes_detalle.idconflicto =
                   conflictos_entre_viajes.id))
        INNER JOIN prod_sca_pista_aeropuerto_2.viajesnetos viajesnetos
           ON     (conflictos_entre_viajes_detalle.idviaje_neto =
                      viajesnetos.IdViajeNeto)
              AND (conflictos_entre_viajes_detalle_1.idviaje_neto =
                      viajesnetos.IdViajeNeto))
       INNER JOIN
       (SELECT max(date_format(timestamp, '%Y-%m-%d')) AS maximo
          FROM conflictos_entre_viajes conflictos_entre_viajes)
       Subquery
          ON (date_format(timestamp, '%Y-%m-%d') = Subquery.maximo)
      
      
      ) as cevd on cevd.idviaje_neto = v.IdViajeNeto
            
       left join (
       
      
       SELECT 
       conflictos_entre_viajes.id,
       group_concat(if(viajesnetos.Code IS NULL,
          concat(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada),
          viajesnetos.Code))
          AS identifiacador
  FROM ((conflictos_entre_viajes_detalle conflictos_entre_viajes_detalle
         INNER JOIN viajesnetos viajesnetos
            ON (conflictos_entre_viajes_detalle.idviaje_neto =
                   viajesnetos.IdViajeNeto))
        INNER JOIN
        conflictos_entre_viajes conflictos_entre_viajes
           ON (conflictos_entre_viajes_detalle.idconflicto =
                  conflictos_entre_viajes.id))
       INNER JOIN
       (SELECT max(date_format(timestamp, '%Y-%m-%d')) AS maximo
          FROM conflictos_entre_viajes conflictos_entre_viajes)
       Subquery
          ON (date_format(timestamp, '%Y-%m-%d') = Subquery.maximo)
          group by  conflictos_entre_viajes.id
     
        
        ) as cev
                 on(cev.id = cevd.idconflicto)
      WHERE
          v.Estatus " . $estatus  . "
      AND
      CAST(CONCAT(v.FechaLlegada,
                    ' ',
                    v.HoraLlegada)
            AS DATETIME) between '{$timestamp_inicial}' and '{$timestamp_final}'
      AND v.IdViajeNeto not in (select IdViajeNeto from viajesrechazados)
      group by IdViajeNeto
      ORDER BY v.FechaLlegada, camion, v.HoraLlegada, idEstatus
      ";
        dd($timestamp_inicial);
        return DB::connection('sca')->select(DB::raw($SQL));
    }
}