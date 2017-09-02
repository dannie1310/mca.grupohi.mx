<?php     ini_set('memory_limit','2048M'); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<body>
@if(count($data))
<table width="1300" border="0" align="center" >
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"><div align="right"><font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo 'FECHA DE CONSULTA '.date("d-m-Y")."/".date("H:i:s",time()); ?></font></div></td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"  border="0" align="center">
            <div align="left">
                <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;">VIAJES NETOS DEL PERÍODO (</font>
                @if (Auth::user()->can(['visualizar-reporte-diario-viajes-netos']))
                    <font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaInicial'] . ' ' . $request['HoraInicial']; ?></font>
                    <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"> AL </font><font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaInicial'] . ' ' . $request['HoraFinal']; ?>)</font>
                @else
                    <font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaInicial'] . ' ' . $request['HoraInicial']; ?></font>
                    <font color="#000000" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"> AL </font><font color="#666666" style="font-family:'Trebuchet MS'; font-weight:bold;font-size:14px;"><?PHP echo $request['FechaFinal'] . ' ' . $request['HoraFinal']; ?>)</font>
                @endif

            </div></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2"><font color="#000000" face="Trebuchet MS" style="font-size:12px; ">OBRA:</font>&nbsp;<font color="#666666" face="Trebuchet MS" style="font-size:12px; "><?php echo \App\Models\Proyecto::find(\App\Facades\Context::getId())->descripcion ?></font></td>
    </tr>

    <tr>
        <td colspan="2"><font color="#000000" face="Trebuchet MS" style="font-size:12px; ">FECHA:</font> &nbsp;<font color="#666666" face="Trebuchet MS" style="font-size:12px; "><?php echo date("d-m-Y"); ?></font></td>
    </tr>
    <tr>
        <td colspan="2"><table width="2000" border="0" align="center" >
                <tr>
                    <td >&nbsp;</td>
                </tr>
                <tr>

                        <td colspan="24">&nbsp;</td>
                        <td colspan="3" bgcolor="969696">
                            <div align="center">
                                <font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Tarifa</font></font>
                            </div>
                        </td>


                </tr>
                <tr bgcolor="#0A8FC7">
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">#</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Creo Primer Toque</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Creo Segundo Toque</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cubicaci&oacute;n Cami&oacute;nm<sup>3</sup></font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cubicaci&oacute;n Viaje Neto m<sup>3</sup></font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cubicaci&oacute;n Viaje m<sup>3</sup></font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Cami&oacute;n</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Placas Cami&oacute;n</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Placas Caja</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Sindicato Camion</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Sindicato Viaje</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Empresa Viaje</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Salida</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Hora Salida</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Fecha Llegada</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Hora Llegada</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Turno</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">D&iacute;a de aplicaci&oacute;n</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Or&iacute;gen</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Destino</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Material</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Tiempo</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Ruta</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Distancia (Km)</font></div></td>
                    <td bgcolor="C0C0C0"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">1er Km </font></div></td>
                    <td bgcolor="C0C0C0"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Km Sub. </font></div></td>
                    <td bgcolor="C0C0C0"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Km Adc.</font></div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Importe</font> </div></td>
                    <td bgcolor="969696"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px; font-weight:bold ">Ticket</font> </div></td>

                </tr>
                <?php $key_global =0; ?>
                @foreach($data as $chunk)
                @foreach($chunk as $key => $item)
                    <?php
                    $key_global++;
                    if($item->Hora >= '00:00:00' && $item->Hora < '07:00:00'){
                        $fechaAplica = strtotime ( '-1 day' , strtotime ( $item->Fecha ) ) ;
                        $fechaAplica = date ( 'd-m-Y' , $fechaAplica );
                    }
                    else {
                        $fechaAplica = $item->Fecha;
                    }
                    $dia = date('N',strtotime($item->Fecha));
                //echo $dia;

                if($item->Hora >= '00:00:00' && $item->Hora < '07:00:00'){
                    $fechaAplica = strtotime ( '-1 day' , strtotime ( $item->Fecha ) ) ;
                    $fechaAplica = date ( 'd-m-Y' , $fechaAplica );
                }
                else {
                    $fechaAplica = $item->Fecha;
                }
                ?>
                <tr <?php if($item->conflictos!=''): ?> style="background-color: #FCC" <?php endif; ?> >
                    <td width="1"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $key_global ; ?></font></div></td>
                    <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->primer_toque; ?></font></div></td>
                    <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo ($item->IdPerfil != 3) ? $item->segundo_toque : "N/A"; ?></font></div></td>
                    <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->cubicacion; ?></font></div></td>
                    <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->CubicacionViajeNeto; ?></font></div></td>
                    <td width="5"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->CubicacionViaje; ?></font></div></td>
                    <td width="30"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->Camion; ?></font></div></td>
                    <td width="30"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->placas; ?></font></div></td>
                    <td width="30"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->PlacasCaja; ?></font></div></td>

                    <td width="150"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->Empresa; ?></font></div></td>
                    <td width="70"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->SindicatoConci; ?></font></div></td>
                    <td width="150"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->Empresaconci; ?></font></div></td>
                    <td width="50"><div align="center"> <font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->FechaSalida; ?></font></div></td>
                    <td width="50"><div align="center"> <font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->HoraSalida; ?></font></div></td>
                    <td width="50"><div align="center"> <font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->Fecha; ?></font></div></td>
                    <td width="50"><div align="center"> <font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->Hora; ?></font></div></td>
                    <td width="70"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->turno; ?></font></div></td>
                    <td width="60"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $fechaAplica; ?></font></div></td>
                    <td width="40"><div align="center"> <font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->origen; ?></font></div></td>
                    <td width="90"><div align="center"> <font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->Tiro; ?></font></div></td>
                    <td width="70"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->material; ?></font></div></td>
                    <td width="50"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->tiempo_mostrar; ?></font></div></td>
                    <td width="20"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->ruta; ?></font></div></td>
                    <td width="20"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo $item->distancia; ?></font></div></td>
                    <td width="30"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo number_format($item->tarifa_material_pk,2,".",",");; ?></font></div></td>
                    <td width="30"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo number_format($item->tarifa_material_ks,2,".",",");; ?></font></div></td>
                    <td width="30"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo number_format($item->tarifa_material_ka,2,".",","); ?></font></div></td>
                    <td width="50"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;"><?php echo number_format($item->ImporteTotal_M,2,".",","); ?></font></div></td>

                    <td width="20"><div align="center"><font color="#000000" face="Trebuchet MS" style="font-size:10px;">'<?php echo $item->code; ?></font></div></td>

                </tr>
                      @endforeach
                      @endforeach
                    @else
                            <table width="600" align="center" >
                            <tr>
                                <td class="Titulo">NO EXISTEN ACARREOS EJECUTADOS EN EL PERIODO: </td>
                            </tr>
                            <tr>
                                @if (Auth::user()->can(['visualizar-reporte-diario-viajes-netos']))
                                    <td class="Titulo">DEL:<span class="Estilo1"> <?PHP echo $request['FechaInicial'] . ' ' . $request['HoraInicial']; ?> </span>AL: <span class="Estilo1"><?PHP echo $request['FechaInicial'] . ' ' . $request['HoraFinal']; ?>)</span></font></td>
                                @else
                                    <td class="Titulo">DEL:<span class="Estilo1"> <?PHP echo $request['FechaInicial'] . ' ' . $request['HoraInicial']; ?> </span>AL: <span class="Estilo1"><?PHP echo $request['FechaFinal'] . ' ' . $request['HoraFinal']; ?>)</span></font></td>
                                @endif
                            </tr>

                            <tr>
                                <td class="Titulo">&nbsp;</td>
                            </tr>
                        </table>
    @endif
</body>
</html>