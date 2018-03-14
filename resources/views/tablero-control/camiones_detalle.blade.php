@extends('layout')

@section('content')
    @if($tipo == 10)
        <h1>CAMBIO DE CUBICACIÓN EN LOS CAMIONES</h1>
    @endif

    <h5>Fecha:{{$fecha_f}}</h5>
    <hr>

    <div class="table-responsive">
        <table class="table table-hover table-bordered small">
            <thead>
            <tr>
                <th>Económico</th>
                <th>Placas</th>
                <th>Cubicación para Pago Actual</th>
                <th>Cubicación Real Actual</th>
                <th>Cubicación para Pago Anterior</th>
                <th>Cubicación Real Anterior</th>
                <th>Fecha</th>
            </tr>
            </thead>
            <tbody>

            @foreach($datos as $d)
                <tr>
                    <td>{{ $d->Economico }}</td>
                    <td>{{ $d->Placas }}</td>
                    <td><div align="right">{{ $d->cubicacionPagoActual }}</div></td>
                    <td><div align="right">{{ $d->cubicacionRealActual }}</div></td>
                    <td><div align="right">{{ $d->cubicacionPago}}</div></td>
                    <td><div align="right">{{ $d->cubicacionReal }}</div></td>
                    <td>{{ $d->FechaHoraAprobo }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="text-center">
            {!! $datos->appends(['buscar' => $busqueda])->render() !!}
        </div>
    </div>
@stop
