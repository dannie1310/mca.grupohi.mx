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
                    <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;">INICIO DE VIAJES DEL PER&Iacute;ODO (</font>
                    <font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaInicial'] . ' ' . $request['HoraInicial']; ?></font>
                    <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"> AL </font><font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaFinal'] . ' ' . $request['HoraFinal']; ?>)</font></div></td>
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
            <td colspan="2"><table width="1300" border="1" align="center" >

                    <tr bgcolor="#0A8FC7">
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">#</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Origen</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cami&oacute;n</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Placas Cami&oacute;n</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cubicaci&oacute;n m<sup>3</sup></font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Material</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Inicio</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Checador</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Perfil</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Turno</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Folio Mina</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Folio Seguimiento</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Ticket</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Deductiva</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Motivo Deductiva</font></div></td>
                        <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Tipo de Viaje</font></div></td>


                    </tr>
                    @foreach($data as $key => $item)

                        <tr>
                            <td width="1"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $key + 1; ?></font></div></td>
                            <td width="5"><div align="left"><font color="#000000" face="Trebuchet MS" style="font-size:10px;">&nbsp;&nbsp;<?php echo $item->origen; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->camion; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->placas; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->cubicacion; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->material; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->fechaorigen; ?></font></div></td>
                            <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->usuario; ?></font></div></td>
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->perfil; ?></font></div></td>
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->turno; ?></font></div></td>
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->folioMina; ?></font></div></td>
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->folioSeguimiento; ?></font></div></td>
                            @if($item->tipo==0)
                                <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;">No Aplica</font></div></td>
                            @else
                                <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->code; ?></font></div></td>
                            @endif
                            @if($item->deductiva == 0)
                                <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;">0</font></div></td>
                                <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;">No aplica</font></div></td>
                            @else
                                <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->deductiva; ?></font></div></td>
                                <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->motivo; ?></font></div></td>
                            @endif
                            <td width="10"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->tipo_viaje; ?></font></div></td>
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