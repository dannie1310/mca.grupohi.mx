@extends('layout')

@section('content')
    @if($tipo == 3)
        <h1>11111</h1>
    @elseif($tipo == 2)
        <h1>VIAJES VALIDADOS Y NO CONCILIADOS</h1>
    @endif

    <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Económico</th>
                <th>Origen</th>
                <th>Material</th>
                <th>Cubicación</th>
                <th>Fecha Salida</th>
                <th>Destino</th>
                <th>Fecha Llegada</th>
                <th>Ticket</th>
                <th>Folio Mina</th>
                <th>Folio Seguimiento</th>
                <th>Alerta</th>
            </tr>
            </thead>
            <tbody>
            {{--@foreach($telefono as $t)--}}
                <tr>
                    <td>AQdatos</td>
                </tr>
            {{--@endforeach--}}
            </tbody>
        </table>

    </div>
@stop
