@include('partials.errors')
@extends('layout')

@section('content')
    <div class='success'></div>
    <h1>TABLERO DE CONTROL</h1>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Condición de análisis</th>
                <th><div align="center">Total</div></th>
                <th><div align="center">Señal</div></th>
                <th><div align="center">Acciones</div></th>
            </tr>
            </thead>
            <tbody>
                 <tr>
                     <td>Viajes: No validados y No conciliados</td>
                     <td><div align="center">{{number_format($no_validados + $no_validados_total ,0,".",",")  }}</div></td>
                     @if($no_validados_total > 0)
                         <td><div align="center"><button type="button" class="btn btn-danger btn-circle"></button></div></td>
                     @elseif($no_validados > 0)
                         <td><div align="center"><button type="button" class="btn btn-warning btn-circle"></button></div></td>
                     @else
                         <td><div align="center"><button type="button" class="btn btn-success btn-circle"></button></div></td>
                     @endif
                     <td><div align="center">
                         <a href="{{ route('tablero-detalle.show',1) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a></div>
                     </td>
                 </tr>
                 <tr>
                     <td>Viajes: Validados y No conciliados</td>
                     <td><div align="center">{{number_format($validados + $validados_total,0,".",",")  }}</div></td>
                     @if($validados > 0)
                         <td><div align="center"> <button type="button" class="btn btn-danger btn-circle"></button></div></td>
                     @elseif($validados_total > 0)
                         <td><div align="center"><button type="button" class="btn btn-warning btn-circle"></button></div></td>
                     @else
                         <td><div align="center"><button type="button" class="btn btn-success btn-circle"></button></div></td>
                     @endif
                     <td width="20"><div align="center">
                         <a href="{{ route('tablero-detalle.show',2) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a></div>
                     </td>
                 </tr>
                 <tr>
                     <td>Usuarios con diferentes IMEI</td>
                     <td><div align="center">{{number_format(($usuario_imei/2),0,".",",")  }}</div></td>
                     @if(($usuario_imei/2) > 0)
                         <td><div align="center"> <button type="button" class="btn btn-danger btn-circle"></button></div></td>
                     @else
                         <td><div align="center"><button type="button" class="btn btn-success btn-circle"></button></div></td>
                     @endif
                     <td width="20"><div align="center">
                             <a href="{{ route('tablero-detalle.show',3) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a></div>
                     </td>
                 </tr>
                 <tr>
                     <td>IMEI con diferentes usuarios</td>
                     <td><div align="center">{{number_format(($imei_usuario/2),0,".",",")  }}</div></td>
                     @if(($imei_usuario/2) > 0)
                         <td><div align="center"> <button type="button" class="btn btn-danger btn-circle"></button></div></td>
                     @else
                         <td><div align="center"><button type="button" class="btn btn-success btn-circle"></button></div></td>
                     @endif
                     <td width="20"><div align="center">
                             <a href="{{ route('tablero-detalle.show',4) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a></div>
                     </td>
                 </tr>
                 <tr>
                     <td>IMEI con diferentes impresora</td>
                     <td><div align="center">{{number_format(($imei_impresora/2),0,".",",")  }}</div></td>
                     @if(($imei_impresora/2) > 0)
                         <td><div align="center"> <button type="button" class="btn btn-danger btn-circle"></button></div></td>
                     @else
                         <td><div align="center"><button type="button" class="btn btn-success btn-circle"></button></div></td>
                     @endif
                     <td width="20"><div align="center">
                             <a href="{{ route('tablero-detalle.show',5) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a></div>
                     </td>
                 </tr>
                 <tr>
                     <td>Impresora con diferentes IMEI</td>
                     <td><div align="center">{{number_format(($impresora_imei/2),0,".",",")  }}</div></td>
                     @if(($impresora_imei/2) > 0)
                         <td><div align="center"> <button type="button" class="btn btn-danger btn-circle"></button></div></td>
                     @else
                         <td><div align="center"><button type="button" class="btn btn-success btn-circle"></button></div></td>
                     @endif
                     <td width="20"><div align="center">
                             <a href="{{ route('tablero-detalle.show',6) }}" title="Detalle" class="btn btn-xs btn-show"><i class="fa fa-eye"></i></a></div>
                     </td>
                 </tr>
            </tbody>
        </table>
    </div>
@stop