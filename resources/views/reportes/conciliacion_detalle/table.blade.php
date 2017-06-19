<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>
@if(count($data))
    <table width="1000" border="0" align="center" >

        <tr>
            <td colspan="2"><div align="right"><font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo 'FECHA DE CONSULTA '.date("d-m-Y")."/".date("H:i:s",time()); ?></font></div></td>
        </tr>
        <tr>
            <td colspan="2"  align="center">
                <div align="left">
                    @if($request['Codigo'] == " ")
                        <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;">CONCILIACIONES DEL PER&Iacute;ODO (</font>
                        <font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaInicial'] . ' ' . $request['HoraInicial']; ?></font>
                        <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"> AL </font><font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaFinal'] . ' ' . $request['HoraFinal']; ?>)</font></div></td>

                    @else
                        <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;">CONCILIACIONES DEL FOLIO #</font>
                        <font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['Codigo']; ?></font>
                        </div></td>
                    @endif
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2"><font color="#000000" face="Trebuchet MS" style="font-size:12px; ">OBRA:</font>&nbsp;<font color="#666666" face="Trebuchet MS" style="font-size:12px; "><?php echo \App\Models\Proyecto::find(\App\Facades\Context::getId())->descripcion ?></font></td>
        </tr>

        <tr>
            <td colspan="2"><font color="#000000" face="Trebuchet MS" style="font-size:12px; ">FECHA:</font> &nbsp;<font color="#666666" face="Trebuchet MS" style="font-size:12px; "><?php echo date("d-m-Y"); ?></font></td>
        </tr>
        <tr>
            <td colspan="2"><table width="1900" border="1" align="right" >

                    <tr bgcolor="#0A8FC7">
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Folio Conciliaci&oacute;n</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Folio Conciliaci&oacute;n Historico</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Conciliaci&oacute;n</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Registro</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Empresa</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Sindicato</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Code</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Carga Viaje</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Salida Viaje</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Llegada Viaje</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cami&oacute;n</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cubicacion Cami&oacute;n Viaje</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cubicacion Cami&oacute;n</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Importe Viaje</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Estado de la Conciliaci&oacute;n</font></div></td>


                    </tr>
                    @foreach($data as $key => $item)

                        <tr>
                            <td width="3"><div align="left"><font color="#000000" face="Trebuchet MS" style="font-size:10px;">&nbsp;&nbsp;<?php echo $item->folio_conciliacion; ?></font></div></td>
                            <td width="3"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->folio_conciliacion_historico; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->fecha_conciliacion; ?></font></div></td>
                            <td width="15"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->fecha_registro_conciliacion; ?></font></div></td>
                            <td width="15"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->empresa; ?></font></div></td>
                            <td width="15"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->sindicato; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->code; ?></font></div></td>
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->fecha_carga_viaje; ?>&nbsp;<?php echo $item->hora_carga_viaje; ?></font></div></td>
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->fecha_salida_viaje; ?>&nbsp;<?php echo $item->hora_salida_viaje; ?></font></div></td>
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->fecha_llegada; ?>&nbsp;<?php echo $item->hora_llegada; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->camion; ?></font></div></td>
                            <td width="3"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->cubicacion_camion_viaje; ?></font></div></td>
                            <td width="3"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->cubicacion_camion; ?></font></div></td>
                            <td width="3"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->importe_viaje; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->estado_conciliacion; ?></font></div></td>
                        </tr>
                    @endforeach
                    @else
                        <table width="600" align="center" >
                            <tr>
                                <td class="Titulo">NO EXISTEN VIAJES INICIADOS EN ESTE PERIODO: </td>
                            </tr>
                            <tr>
                                <td class="Titulo">DEL:<span class="Estilo1"> <?PHP echo $request['FechaInicial'] . ' ' . $request['HoraInicial']; ?> </span>AL: <span class="Estilo1"><?PHP echo $request['FechaFinal'] . ' ' . $request['HoraFinal']; ?>)</span></font></td>
                            </tr>

                            <tr>
                                <td class="Titulo">&nbsp;</td>
                            </tr>
                        </table>
@endif
</body>
</html>