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
        ini_set('memory_limit','2048M');

        $timestamp_final = $request->get('FechaInicial') . ' ' . $horaFinal;

        $timestamp_inicial = $request->get('FechaInicial') . ' ' . $horaInicial;

        $SQL = "SELECT 
    DATE_FORMAT(v.FechaLlegada, '%d-%m-%Y') AS Fecha,
    DATE_FORMAT(v.FechaSalida, '%d-%m-%Y') AS FechaSalida,
    DATE_FORMAT(v.HoraSalida, '%H:%i:%s') AS HoraSalida,
    t.IdTiro,
    cpc.name AS Perfil,
    cpc.id AS IdPerfil,
    t.Descripcion AS Tiro,
    c.IdCamion AS IdCamion,
    c.Economico AS Camion,
    v.IdViajeNeto AS IdViajeNeto,
    v.estatus AS idEstatus,
    v.code,
    IF(vi.estatus IS NOT NULL,
        'Validado',
        IF(vr.estatus IS NOT NULL,
            'Rechazado',
            'Pendiente Validar')) AS Estatus,
    v.HoraLlegada AS Hora,
    v.code,
    c.CubicacionParaPago AS cubicacion,
    v.CubicacionCamion AS CubicacionViajeNeto,
    vi.CubicacionCamion AS CubicacionViaje,
    o.Descripcion AS origen,
    o.IdOrigen AS idorigen,
    m.Descripcion AS material,
    m.IdMaterial AS idmaterial,
    sin.Descripcion AS Sindicato,
    sinca.Descripcion AS SindicatoCamion,
    emp.razonSocial AS Empresa,
    sincon.Descripcion AS SindicatoConci,
    empcon.razonSocial AS Empresaconci,
    TIMEDIFF((CONCAT(v.FechaLlegada, ' ', v.HoraLlegada)),
            (CONCAT(v.FechaSalida, ' ', v.HoraSalida))) AS tiempo_mostrar,
    ROUND((HOUR(TIMEDIFF(v.HoraLlegada, v.HoraSalida)) * 60) + (MINUTE(TIMEDIFF(v.HoraLlegada, v.HoraSalida))) + (SECOND(TIMEDIFF(v.HoraLlegada, v.HoraSalida)) / 60),
            2) AS tiempo,
    CONCAT('R-', r.IdRuta) AS ruta,
    r.TotalKM AS distancia,
    r.IdRuta AS idruta,
    tm.IdTarifa AS tarifa_material,
    tm.PrimerKM AS tarifa_material_pk,
    tm.KMSubsecuente AS tarifa_material_ks,
    tm.KMAdicional AS tarifa_material_ka,
    IF(vi.IdViaje IS NOT NULL,
        vi.Importe,
        ((tm.PrimerKM * 1 * IF(v.CubicacionCamion <= 8,
            c.CubicacionParaPago,
            v.CubicacionCamion)) + (tm.KMSubsecuente * r.KmSubsecuentes * IF(v.CubicacionCamion <= 8,
            c.CubicacionParaPago,
            v.CubicacionCamion)) + (tm.KMAdicional * r.KmAdicionales * IF(v.CubicacionCamion <= 8,
            c.CubicacionParaPago,
            v.CubicacionCamion)))) AS ImporteTotal_M,
    GROUP_CONCAT(conci.idconciliacion) AS idconciliacion,
    GROUP_CONCAT(conci.fecha_conciliacion) AS fecha_conciliacion,
    conci.fecha_inicial,
    conci.fecha_final,
    conci.estado,

 IF(v.HoraLlegada >= '07:00:00'
            AND v.HoraLlegada < '19:00:00',
        'Primer Turno',
        'Segundo Turno') AS turno,





    vi.IdViaje,
    c.placas,
    c.PlacasCaja,
    v.CreoPrimerToque,
    v.Creo,
    cev.identifiacador AS conflictos,
    CONCAT(usuario1.nombre,
            ' ',
            usuario1.apaterno,
            ' ',
            usuario1.amaterno) AS primer_toque,
    CONCAT(usuario2.nombre,
            ' ',
            usuario2.apaterno,
            ' ',
            usuario2.amaterno) AS segundo_toque,
    v.imei,
    cpc.name AS perfil,
    cpc.id AS IdPerfil,
    CASE conci.estado
		WHEN 0 THEN 'Generada'
        WHEN 1 THEN 'Cerrada'
        WHEN 2 THEN 'Aprobada'
        ELSE 'Cancelada'
    END AS estado_string,
    v.folioMina,
    v.folioSeguimiento,
    CASE
        WHEN
            v.tipoViaje = 1
        THEN
            'Origen (Mina)'
        ELSE 'Entrada'
    END AS tipo_viaje
FROM
    viajesnetos AS v
        JOIN
    tiros AS t USING (IdTiro)
        JOIN
    camiones AS c USING (IdCamion)
        LEFT JOIN
    viajesrechazados vr ON vr.IdViajeNeto = v.IdViajeNeto
        LEFT JOIN
    origenes AS o ON (v.IdOrigen = o.IdOrigen)
        JOIN
    materiales AS m ON (v.IdMaterial = m.IdMaterial)
        LEFT JOIN
    tarifas AS tm ON (tm.IdMaterial = m.IdMaterial

        AND tm.Estatus = 1
        AND tm.InicioVigencia < v.FechaLlegada
        AND IFNULL(tm.FinVigencia, NOW()) > v.FechaLlegada)
        LEFT JOIN
    (SELECT 
        *
    FROM
        rutas
    GROUP BY IdOrigen , IdTiro) AS r ON (v.IdOrigen = r.IdOrigen


        AND v.IdTiro = r.IdTiro)
        LEFT JOIN
    sindicatos AS sinca ON sinca.IdSindicato = c.IdSindicato
        LEFT JOIN
    viajes AS vi ON vi.IdViajeNeto = v.IdViajeNeto
        LEFT JOIN
    sindicatos AS sin ON sin.IdSindicato = vi.IdSindicato
        LEFT JOIN
    empresas AS emp ON emp.IdEmpresa = vi.IdEmpresa
        LEFT JOIN
    conciliacion_detalle AS conde ON (conde.idviaje = vi.IdViaje
        AND conde.estado = 1)
        LEFT JOIN
    conciliacion AS conci ON (conci.idconciliacion = conde.idconciliacion
        AND conci.estado = 2)
        LEFT JOIN
    sindicatos AS sincon ON sincon.IdSindicato = conci.IdSindicato
        LEFT JOIN

    empresas AS empcon ON empcon.IdEmpresa = conci.IdEmpresa
	
        LEFT JOIN
		

    igh.usuario AS usuario1 ON usuario1.idusuario = v.CreoPrimerToque
        LEFT JOIN

    igh.usuario AS usuario2 ON usuario2.idusuario = v.Creo
        LEFT JOIN
    configuracion_perfiles_cat AS cpc ON cpc.id = v.IdPerfil
        LEFT JOIN
    (SELECT 
        conflictos_entre_viajes_detalle.idviaje_neto,
            conflictos_entre_viajes.id AS idconflicto
    FROM
        (((conflictos_entre_viajes_detalle conflictos_entre_viajes_detalle_1
    INNER JOIN conflictos_entre_viajes conflictos_entre_viajes ON (conflictos_entre_viajes_detalle_1.idconflicto = conflictos_entre_viajes.id))
    INNER JOIN conflictos_entre_viajes_detalle conflictos_entre_viajes_detalle ON (conflictos_entre_viajes_detalle.idconflicto = conflictos_entre_viajes.id))
    INNER JOIN viajesnetos viajesnetos ON (conflictos_entre_viajes_detalle.idviaje_neto = viajesnetos.IdViajeNeto)
        AND (conflictos_entre_viajes_detalle_1.idviaje_neto = viajesnetos.IdViajeNeto))
    INNER JOIN (SELECT 
        MAX(DATE_FORMAT(timestamp, '%Y-%m-%d')) AS maximo
    FROM
        conflictos_entre_viajes conflictos_entre_viajes) Subquery ON (DATE_FORMAT(timestamp, '%Y-%m-%d') = Subquery.maximo)) AS cevd ON cevd.idviaje_neto = v.IdViajeNeto
        LEFT JOIN
    (SELECT 
        conflictos_entre_viajes.id,
            GROUP_CONCAT(IF(viajesnetos.Code IS NULL, CONCAT(viajesnetos.FechaLlegada, ' ', viajesnetos.HoraLlegada), viajesnetos.Code)) AS identifiacador
    FROM
        ((conflictos_entre_viajes_detalle conflictos_entre_viajes_detalle
    INNER JOIN viajesnetos viajesnetos ON (conflictos_entre_viajes_detalle.idviaje_neto = viajesnetos.IdViajeNeto))
    INNER JOIN conflictos_entre_viajes conflictos_entre_viajes ON (conflictos_entre_viajes_detalle.idconflicto = conflictos_entre_viajes.id))
    INNER JOIN (SELECT 
        MAX(DATE_FORMAT(timestamp, '%Y-%m-%d')) AS maximo
    FROM
        conflictos_entre_viajes conflictos_entre_viajes) Subquery ON (DATE_FORMAT(timestamp, '%Y-%m-%d') = Subquery.maximo)
    GROUP BY conflictos_entre_viajes.id) AS cev ON (cev.id = cevd.idconflicto)
      WHERE
         
      CAST(CONCAT(v.FechaLlegada,
                    ' ',
                    v.HoraLlegada)
            AS DATETIME) between '{$timestamp_inicial}' and '{$timestamp_final}'
      group by IdViajeNeto
      ORDER BY v.FechaLlegada, camion, v.HoraLlegada, idEstatus
      ";
       // dd($SQL);
        $r = collect(DB::connection('sca')->select(DB::raw($SQL)))->chunk(1000);

	  DB::connection('sca')->disableQueryLog();
        return $r;
    }
}
